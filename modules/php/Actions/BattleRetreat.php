<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

// Rename to select unit?
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
    // Return commanders to their stacks
    Units::getInLocationLike(COMMANDER, 'commander_rerolls_track');
    $commanders = $this->getCommandersOnRerollsTrack();
    $spaceId = Globals::getActiveBattleSpaceId();

    foreach($commanders as $faction => $unit) {
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

  public function argsBattleRetreat()
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

    Notifications::moveStack(self::getPlayer(), $units, $markers, $fromSpace, $space, true);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

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

    $possibleRetreatOptions = $space->getAdjacentConnectionsAndSpaces();

    if ($hasFleets) {
      // Fleet retreat priorities
      Notifications::log('Fleet retreat', []);
    } else if ($isAttacker) {
      // Attacker retreat priorities
      Notifications::log('Attacker retreat', []);
      $optionsFriendlyStackEnteredFrom = Utils::filter($possibleRetreatOptions, function ($data) use ($spaceIdsAttackersEnteredFrom) {
        return in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
      });
      $optionsFriendlyStackEnteredFrom = $this->filterConnectionRestrictions($optionsFriendlyStackEnteredFrom, $attackerUnits);

      return array_map(function ($data) {
        return $data['space'];
      }, $optionsFriendlyStackEnteredFrom);
    } else {
      // Defender retreat priorities
      Notifications::log('Defender retreat', []);

      $possibleRetreatOptions = Utils::filter($possibleRetreatOptions, function ($data) use ($spaceIdsAttackersEnteredFrom) {
        return !in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
      });

      $possibleRetreatOptions = $this->filterConnectionRestrictions($possibleRetreatOptions, $isAttacker ? $attackerUnits : $defenderUnits);

      return $this->getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleRetreatOptions, $faction);
    }
    return [];
  }

  private function getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleConnections, $faction)
  {
    $spaces = Spaces::getMany(array_keys($possibleConnections))->toArray();
    $enemyFaction = Players::otherFaction($faction);

    $spacesWithoutEnemyUnits = Utils::filter($spaces, function ($space) use ($enemyFaction) {
      return count($space->getUnits($enemyFaction)) === 0;
    });

    $homeSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      return $space->getHomeSpace() === $faction;
    });

    if (count($homeSpaces) > 0) {
      return $homeSpaces;
    }

    return [];
  }
}
