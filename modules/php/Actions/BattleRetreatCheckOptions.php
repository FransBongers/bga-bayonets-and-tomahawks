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

class BattleRetreatCheckOptions extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_RETREAT_CHECK_OPTIONS;
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

  public function stBattleRetreatCheckOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);
    $units = Utils::filter($space->getUnits($faction), function ($unit) {
      return !$unit->isFort();
    });
    if (count($units) === 0 || ($faction === FRENCH && $space->hasBastion())) {
      // No units to retreat
      $this->resolveAction(['automatic' => true]);
      return;
    }

    $retreatOptions = $this->getRetreatOptions();

    if (count($retreatOptions) > 0) {
      $this->insertRetreatAction($retreatOptions);
      $this->resolveAction(['automatic' => true]);
      return;
    }

    // delete all non light units
    $player = self::getPlayer();
    $nonLightUnits = Utils::filter($units, function ($unit) {
      return !$unit->isLight();
    });

    if (count($nonLightUnits) > 0) {
      Notifications::message('${player_name} cannot comply with any Retreat Priority. All non-Light units are eliminated', ['player' => $player]);
      foreach ($nonLightUnits as $unit) {
        $unit->eliminate($player);
      }
    }

    $retreatOptions = $this->getRetreatOptions();

    if (count($retreatOptions) > 0) {
      $this->insertRetreatAction($retreatOptions);
      $this->resolveAction(['automatic' => true]);
      return;
    }

    // delete all light units
    $lightUnits = Utils::filter($units, function ($unit) {
      return $unit->isLight();
    });

    if (count($lightUnits) > 0) {
      Notifications::message('${player_name} cannot comply with any Retreat Priority. All Light units are eliminated', ['player' => $player]);
      foreach ($lightUnits as $unit) {
        $unit->eliminate($player);
      }
    }

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRetreatCheckOptions()
  {
    // Return commanders to their stacks
    Units::getInLocationLike(COMMANDER, 'commander_rerolls_track');
    $commanders = $this->getCommandersOnRerollsTrack();
    $spaceId = Globals::getActiveBattleSpaceId();

    foreach ($commanders as $faction => $unit) {
      if ($unit === null) {
        continue;
      }
      $unit->setLocation($spaceId);
      Notifications::battleReturnCommander(Players::getPlayerForFaction($faction), $unit, $spaceId);
    }
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRetreatCheckOptions()
  {
    $info = $this->ctx->getInfo();

    return [
      'retreatOptions' => $this->getRetreatOptions(),
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

  public function actPassBattleRetreatCheckOptions()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleRetreatCheckOptions($args)
  {
    self::checkAction('actBattleRetreatCheckOptions');

    $spaceId = $args['spaceId'];

    $options = $this->getRetreatOptions();

    $space = Utils::array_find($options, function ($space) use ($spaceId) {
      return $space->getId() === $spaceId;
    });

    if ($space === null) {
      throw new \feException("ERROR 013");
    }

    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $fromSpace = Spaces::get($info['spaceId']);

    $units = $fromSpace->getUnits($faction);

    Units::move(array_map(function ($unit) {
      return $unit->getId();
    }, $units), $spaceId);

    $markers = Markers::getInLocation(Locations::stackMarker($fromSpace->getId(), $faction))->toArray();
    Markers::move(array_map(function ($marker) {
      return $marker->getId();
    }, $markers), Locations::stackMarker($space->getId(), $faction));

    Notifications::moveStack(self::getPlayer(), $units, $markers, $fromSpace, $space, null, true);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function insertRetreatAction($retreatOptions)
  {
    $info = $this->ctx->getInfo();

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => BATTLE_RETREAT,
      'playerId' => $info['playerId'],
      'faction' => $info['faction'],
      'spaceId' => $info['spaceId'],
      'isAttacker' => $info['isAttacker'],
      'retreatOptionIds' => $retreatOptions
    ]));
  }



  private function filterConnectionRestrictions($possibleRetreatOptions, $units)
  {
    return Utils::filter($possibleRetreatOptions, function ($data) use ($units) {
      return $data['connection']->canBeUsedByUnits($units, true);
    });
  }

  private function getRetreatOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $isAttacker = $info['isAttacker'];

    $space = Spaces::get($spaceId);

    $attackerUnits = $isAttacker ? $space->getUnits($faction) : $space->getUnits(Players::otherFaction($faction));
    $defenderUnits = !$isAttacker ? $space->getUnits($faction) : $space->getUnits(Players::otherFaction($faction));
    $spaceIdsAttackersEnteredFrom = array_map(function ($unit) {
      return $unit->getPreviousLocation();
    }, $attackerUnits);
    // $units = $space->getUnits($faction);
    $hasFleets = Utils::array_some($isAttacker ? $attackerUnits : $defenderUnits, function ($unit) {
      return $unit->isFleet();
    });

    $adjacentConnectionsAndSpaces = $space->getAdjacentConnectionsAndSpaces();

    if ($hasFleets) {
      // Fleet retreat priorities
      return BTHelpers::getSpacesBasedOnFleetRetreatPriorities($faction);
    }

    if ($isAttacker) {
      // Attacker retreat priorities
      $optionsFriendlyStackEnteredFrom = Utils::filter($adjacentConnectionsAndSpaces, function ($data) use ($spaceIdsAttackersEnteredFrom) {
        return in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
      });
      $optionsFriendlyStackEnteredFrom = $this->filterConnectionRestrictions($optionsFriendlyStackEnteredFrom, $attackerUnits);

      if (count($optionsFriendlyStackEnteredFrom) > 0) {
        return array_map(function ($data) {
          return $data['space']->getId();
        }, $optionsFriendlyStackEnteredFrom);
      }
    }

    // Adjacent Space priorties

    // If defender filter spaces an enemy stack entered the battle space from this AR
    $possibleRetreatOptions = Utils::filter($adjacentConnectionsAndSpaces, function ($data) use ($spaceIdsAttackersEnteredFrom, $isAttacker) {
      return $isAttacker || !in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
    });

    $possibleRetreatOptions = $this->filterConnectionRestrictions($possibleRetreatOptions, $isAttacker ? $attackerUnits : $defenderUnits);

    return $this->getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleRetreatOptions, $faction);
  }


  private function getSpacesBasedOnAdjacentSpaceRetreatPriorities($adjacentConnectionsAndSpaces, $faction)
  {
    $spaces = array_map(function ($adjacent) {
      return $adjacent['space'];
    }, $adjacentConnectionsAndSpaces);
    $enemyFaction = Players::otherFaction($faction);

    $spacesWithoutEnemyUnits = Utils::filter($spaces, function ($space) use ($enemyFaction) {
      return count($space->getUnits($enemyFaction)) === 0;
    });

    // 1. Friendly Home Space without enemy units
    $homeSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      return $space->getHomeSpace() !== null && $space->getControl() === $faction;
    });

    if (count($homeSpaces) > 0) {
      return BTHelpers::returnSpaceIds($homeSpaces);
    }

    // 2. Friendly space without enemy units
    $friendlySpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      return $space->getControl() === $faction;
    });

    if (count($friendlySpaces) > 0) {
      return BTHelpers::returnSpaceIds($friendlySpaces);
    }

    // 3. A Wilderness Space or friednly Indian Nation Village
    $wildernessSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) {
      return $space->getControl() === NEUTRAL && in_array($space->getIndianVillage(), [CHEROKEE, IROQUOIS]);
    });

    if (count($wildernessSpaces) > 0) {
      return BTHelpers::returnSpaceIds($wildernessSpaces);
    }

    // 4. An enemy-controlled space or enemy Indian Nation Village without enemy units or Militia
    $enemyControlledSpace = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($enemyFaction) {
      return $space->getControl() === $enemyFaction && $space->getMilitia() === 0;
    });

    if (count($enemyControlledSpace) > 0) {
      return BTHelpers::returnSpaceIds($enemyControlledSpace);
    }

    // 5. TODO:

    return [];
  }
}
