<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleRetreat extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_RETREAT;
  }

  // ..######..########....###....########.########
  // .##....##....##......##.##......##....##......
  // .##..........##.....##...##.....##....##......
  // ..######.....##....##.....##....##....######..
  // .......##....##....#########....##....##......
  // .##....##....##....##.....##....##....##......
  // ..######.....##....##.....##....##....########

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function stBattleRetreat()
  {
    $info = $this->ctx->getInfo();
    $retreatOptionIds = $info['retreatOptionIds'];

    if (count($retreatOptionIds) > 1) {
      return;
    }

    // Retreat to single option
    $this->retreat($retreatOptionIds[0]);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRetreat()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRetreat()
  {
    $info = $this->ctx->getInfo();
    $retreatOptionIds = $info['retreatOptionIds'];

    $retreatOptions = [];

    // Add extra check here since args can be executed before state function
    // that should handle the SAIL_BOX case
    if ($retreatOptionIds[0] !== SAIL_BOX) {
      $retreatOptions = Spaces::getMany($retreatOptionIds)->toArray();
    }

    return [
      'retreatOptions' => $retreatOptions,
      'retreatOptionIds' => $retreatOptionIds
    ];
  }

  //  .########..##..........###....##....##.########.########.
  //  .##.....##.##.........##.##....##..##..##.......##.....##
  //  .##.....##.##........##...##....####...##.......##.....##
  //  .########..##.......##.....##....##....######...########.
  //  .##........##.......#########....##....##.......##...##..
  //  .##........##.......##.....##....##....##.......##....##.
  //  .##........########.##.....##....##....########.##.....##

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function actPassBattleRetreat()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleRetreat($args)
  {
    self::checkAction('actBattleRetreat');

    $spaceId = $args['spaceId'];

    $options = $this->argsBattleRetreat()['retreatOptions'];

    $space = Utils::array_find($options, function ($space) use ($spaceId) {
      return $space->getId() === $spaceId;
    });

    if ($space === null) {
      throw new \feException("ERROR 013");
    }

    $this->retreat($spaceId);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function retreat($toSpaceId)
  {
    $info = $this->ctx->getInfo();
    $playerId = $this->ctx->getPlayerId();
    $faction = $info['faction'];

    $fromSpace = Spaces::get($info['spaceId']);

    $units = $fromSpace->getUnits($faction);

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);
    

    // $markers = Markers::getInLocation(Locations::stackMarker($fromSpace->getId(), $faction))->toArray();
    // Markers::move(array_map(function ($marker) {
    //   return $marker->getId();
    // }, $markers), Locations::stackMarker($space->getId(), $faction));

    // Notifications::moveStack(self::getPlayer(), $units, $markers, $fromSpace, $space, null, true);
    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => MOVE_STACK,
      'playerId' => $playerId,
      'fromSpaceId' => $fromSpace->getId(),
      'toSpaceId' => $toSpaceId,
      'unitIds' => $unitIds,
    ]));
  }

  // private function getRetreatOptions()
  // {

  // }

  // private function filterConnectionRestrictions($possibleRetreatOptions, $units)
  // {
  //   return Utils::filter($possibleRetreatOptions, function ($data) use ($units) {
  //     return $data['connection']->canBeUsedByUnits($units, true);
  //   });
  // }

  // private function getRetreatOptions()
  // {
  //   $info = $this->ctx->getInfo();
  //   $faction = $info['faction'];
  //   $spaceId = $info['spaceId'];
  //   $isAttacker = $info['isAttacker'];

  //   $space = Spaces::get($spaceId);

  //   $attackerUnits = $isAttacker ? $space->getUnits($faction) : $space->getUnits(Players::otherFaction($faction));
  //   $defenderUnits = !$isAttacker ? $space->getUnits($faction) : $space->getUnits(Players::otherFaction($faction));
  //   $spaceIdsAttackersEnteredFrom = array_map(function ($unit) {
  //     return $unit->getPreviousLocation();
  //   }, $attackerUnits);
  //   // $units = $space->getUnits($faction);
  //   $hasFleets = Utils::array_some($isAttacker ? $attackerUnits : $defenderUnits, function ($unit) {
  //     return $unit->isFleet();
  //   });

  //   $possibleRetreatOptions = $space->getAdjacentConnectionsAndSpaces();

  //   if ($hasFleets) {
  //     // Fleet retreat priorities
  //     Notifications::log('Fleet retreat', []);
  //     return $this->getSpacesBasedOnFleetRetreatPriorities($faction);
  //   } else if ($isAttacker) {
  //     // Attacker retreat priorities
  //     $optionsFriendlyStackEnteredFrom = Utils::filter($possibleRetreatOptions, function ($data) use ($spaceIdsAttackersEnteredFrom) {
  //       return in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
  //     });
  //     $optionsFriendlyStackEnteredFrom = $this->filterConnectionRestrictions($optionsFriendlyStackEnteredFrom, $attackerUnits);

  //     return array_map(function ($data) {
  //       return $data['space'];
  //     }, $optionsFriendlyStackEnteredFrom);
  //   } else {
  //     // Defender retreat priorities

  //     $possibleRetreatOptions = Utils::filter($possibleRetreatOptions, function ($data) use ($spaceIdsAttackersEnteredFrom) {
  //       return !in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
  //     });

  //     $possibleRetreatOptions = $this->filterConnectionRestrictions($possibleRetreatOptions, $isAttacker ? $attackerUnits : $defenderUnits);

  //     return $this->getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleRetreatOptions, $faction);
  //   }
  //   return [];
  // }

  // private function getSpacesBasedOnFleetRetreatPriorities($faction)
  // {
  //   $spaces = Spaces::getAll()->toArray();
  //   $units = Units::getAll()->toArray();

  //   $coastalSpacesFreeOfEnemyUnits = Utils::filter($spaces, function ($space) use ($units, $faction) {
  //     if (!$space->isCoastal() || $space->getControl() === BTHelpers::getOtherFaction($faction)) {
  //       return false;
  //     }
  //     $spaceId = $space->getId();
  //     return !Utils::array_some($units, function ($unit) use ($spaceId, $faction) {
  //       return $unit->getLocation() === $spaceId && $unit->getFaction() !== $faction;
  //     });
  //   });

  //   // 1. Friendly Coastal Home Space
  //   $friendlyCoastalHomeSpaces = Utils::filter($coastalSpacesFreeOfEnemyUnits, function ($space) use ($faction) {
  //     return $space->getHomeSpace() !== null && $space->getControl() === $faction;
  //   });
  //   if (count($friendlyCoastalHomeSpaces) > 0) {
  //     return $friendlyCoastalHomeSpaces;
  //   }

  //   // Get coastal spaces of friendly sea zones
  //   $friendlySeaZones = GameMap::getFriendlySeaZones($faction);
  //   $coastalSpacesOfFriendlySZ = Utils::filter($coastalSpacesFreeOfEnemyUnits, function ($space) use ($friendlySeaZones) {
  //     return Utils::array_some($friendlySeaZones, function ($friendlySZ) use ($space) {
  //       return in_array($friendlySZ, $space->adjacentSeaZones());
  //     }); 
  //   });

  //   // 2. Frienldy Coastal Space of a friendly SZ
  //   $friendlyCoastalSpaceOfFriendlySZ = Utils::filter($coastalSpacesOfFriendlySZ, function ($space) use ($faction) {
  //     return $space->getControl() === $faction;
  //   });
  //   if (count($friendlyCoastalSpaceOfFriendlySZ) > 0) {
  //     return $friendlyCoastalSpaceOfFriendlySZ;
  //   }

  //   // 3, Wilderness Coastal Space of friendly SZ
  //   $wildernessCoastalSpaceOfFriendlySZ = Utils::filter($coastalSpacesOfFriendlySZ, function ($space) use ($faction) {
  //     return $space->getControl() === NEUTRAL;
  //   });
  //   if (count($wildernessCoastalSpaceOfFriendlySZ) > 0) {
  //     return $wildernessCoastalSpaceOfFriendlySZ;
  //   }

  //   // Return to Sail Box
  //   return [];
  // }

  // private function getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleConnections, $faction)
  // {
  //   $spaces = array_map(function ($adjacent) {
  //     return $adjacent['space'];
  //   }, $possibleConnections);
  //   $enemyFaction = Players::otherFaction($faction);

  //   $spacesWithoutEnemyUnits = Utils::filter($spaces, function ($space) use ($enemyFaction) {
  //     return count($space->getUnits($enemyFaction)) === 0;
  //   });

  //   $homeSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
  //     return $space->getHomeSpace() === $faction;
  //   });

  //   if (count($homeSpaces) > 0) {
  //     return $homeSpaces;
  //   }

  //   return [];
  // }
}
