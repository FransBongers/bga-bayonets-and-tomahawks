<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class RaidMove extends \BayonetsAndTomahawks\Actions\Raid
{
  public function getState()
  {
    return ST_RAID_MOVE;
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

  public function stRaidMove()
  {
    $info = $this->ctx->getInfo();
    $unitId = $info['unitId'];
    $spaceId = $info['toSpaceId'];
    $startSpaceId = $info['startSpaceId'];

    $unit = Units::get($unitId);
    $space = Spaces::get($spaceId);

    $player = self::getPlayer();

    $origin = Spaces::get($unit->getLocation());
    Units::move($unitId, $spaceId);
    $unit->setLocation($spaceId);
    Notifications::moveUnit($player, $unit, $origin, $space);

    $otherPlayer = Players::getOther();
    $otherPlayerFaction = $otherPlayer->getFaction();

    // Check for interception
    $enemyUnits = $space->getUnits($otherPlayerFaction);
    $hasEnemyUnit = count($enemyUnits) > 0;

    if ($hasEnemyUnit) {
      $hasEnemyLightUnit = Utils::array_some($enemyUnits, function ($unitOnSpace) {
        return $unitOnSpace->isLight();
      });

      // Roll for interception
      $dieResult = FLAG;// BTDice::roll();
      $intercepted = ($hasEnemyLightUnit && in_array($dieResult, [FLAG, HIT_TRIANGLE_CIRCLE, B_AND_T])) || $dieResult === FLAG;
      Notifications::interception($otherPlayer, $space, $dieResult, $intercepted);

      // If intercepted move unit back to starting space
      if ($intercepted) {
        $this->returnUnitToStartingSpace($player, $unit, $startSpaceId, $space);
        $this->ctx->updateInfo('resolveParent', true);
      }
    }
    $this->resolveAction(['automatic' => true], true);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreRaidMoved()
  {
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...
}
