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

class BattleOverwhelmDuringRetreat extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_OVERWHELM_DURING_RETREAT;
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

  public function stBattleOverwhelmDuringRetreat() {}

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleOverwhelmDuringRetreat() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleOverwhelmDuringRetreat()
  {
    $options = $this->getOptions();
    $options['units'] = $options['enemyUnits'];
    unset($options['enemyUnits']);
    unset($options['friendlyUnits']);
    return $options;
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

  public function actPassBattleOverwhelmDuringRetreat()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleOverwhelmDuringRetreat($args)
  {
    self::checkAction('actBattleOverwhelmDuringRetreat');

    $unitIds = $args['unitIds'];

    $options = $this->getOptions();

    $player = self::getPlayer();

    foreach ($unitIds as $unitId) {
      $unit = Utils::array_find($options['enemyUnits'], function ($optionUnit) use ($unitId) {
        return $optionUnit->getId() === $unitId;
      });
      if ($unit === null) {
        throw new \feException("ERROR 099");
      }
      $unit->eliminate($player);
    }

    foreach ($options['friendlyUnits'] as $friendlyUnit) {
      $friendlyUnit->eliminate($player);
    }

    $space = $options['space'];

    if ($space->getBattle() === 1) {
      $space->setBattle(0);
      $space->setDefender(null);
      Notifications::battleRemoveMarker($player, $space);
    }

    if ($space->getControl() === $options['faction']) {
      GameMap::updateControl(Players::getOther($player->getId()), $space);
    }
    

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getOptions()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $player = self::getPlayer();
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();

    $units = $space->getUnits();

    $friendlyUnits = [];
    $enemyUnits = [];

    foreach ($units as $unit) {
      if ($unit->isCommander()) {
        continue;
      }
      if ($unit->getFaction() === $playerFaction) {
        $friendlyUnits[] = $unit;
      } else {
        $enemyUnits[] = $unit;
      }
    }
    $friendlyUnitCount = count($friendlyUnits) + $space->getMilitiaForFaction($playerFaction);

    return [
      'enemyUnits' => $enemyUnits,
      'friendlyUnits' => $friendlyUnits,
      'faction' => $playerFaction,
      'numberOfUnitsToEliminate' => min($friendlyUnitCount, count($enemyUnits)),
      'space' => $space,
      'enemyFaction' => BTHelpers::getOtherFaction($playerFaction),
    ];
  }
}
