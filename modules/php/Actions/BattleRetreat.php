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

    Notifications::moveStack(self::getPlayer(), $units, $fromSpace, $space, true);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function filterConnectionRestrictions($possibleConnections, $units)
  {
    $result = [];

    foreach ($possibleConnections as $spaceId => $connection) {
      if ($connection->canBeUsedByUnits($units, true)) {
        $result[$spaceId] = $connection;
      }
    }
    return $result;
  }

  private function getRetreatOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $isAttacker = $info['isAttacker'];

    $space = Spaces::get($spaceId);
    $units = $space->getUnits($faction);

    $hasFleets = Utils::array_some($units, function ($unit) {
      return $unit->isFleet();
    });

    if ($hasFleets) {
      // Fleet retreat priorities
      Notifications::log('Fleet retreat', []);
    } else if ($isAttacker) {
      // Attacker retreat priorities
      Notifications::log('Attacker retreat', []);
    } else {
      // Defender retreat priorities
      Notifications::log('Defender retreat', []);
      $attackerUnits = $space->getUnits(Players::otherFaction($faction));
      $spaceIdsAttackersEnteredFrom = array_map(function ($unit) {
        return $unit->getPreviousLocation();
      }, $attackerUnits);

      $possibleConnections = $space->getAdjacentConnections();
      foreach ($spaceIdsAttackersEnteredFrom as $attackerSpaceId) {
        unset($possibleConnections[$attackerSpaceId]);
      }

      $possibleConnections = $this->filterConnectionRestrictions($possibleConnections, $units);

      return $this->getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleConnections, $faction);
    }
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
