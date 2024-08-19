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

class BattleCombineReducedUnits extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_COMBINE_REDUCED_UNITS;
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

  public function stBattleCombineReducedUnits()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleCombineReducedUnits()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleCombineReducedUnits()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $faction = $info['faction'];

    return [
      'options' => $this->getOptions(Spaces::get($spaceId), $faction),
      'spaceId' => $spaceId,
      'faction' => $faction,
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

  public function actPassBattleCombineReducedUnits()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleCombineReducedUnits($args)
  {
    self::checkAction('actBattleCombineReducedUnits');
    $flipUnitId = $args['flipUnitId'];
    $eliminateUnitId = $args['eliminateUnitId'];
    $unitType = $args['unitType'];

    if ($flipUnitId === $eliminateUnitId) {
      throw new \feException("ERROR 063");
    }

    $stateArgs = $this->argsBattleCombineReducedUnits();

    if (!isset($stateArgs['options'][$unitType])) {
      throw new \feException("ERROR 064");
    }

    $units = $stateArgs['options'][$unitType];

    $unitToFlip = Utils::array_find($units, function ($unit) use ($flipUnitId) {
      return $unit->getId() === $flipUnitId;
    });

    if ($unitToFlip === null) {
      throw new \feException("ERROR 065");
    }

    $unitToEliminate = Utils::array_find($units, function ($unit) use ($eliminateUnitId) {
      return $unit->getId() === $eliminateUnitId;
    });

    if ($unitToEliminate === null) {
      throw new \feException("ERROR 066");
    }

    $player = self::getPlayer();

    $unitToEliminate->eliminate($player);
    $unitToFlip->flipToFull($player);

    $this->checkIfReducedUnitsCanBeCombined(Spaces::get($stateArgs['spaceId']), $stateArgs['faction'], $player);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getOptions($space, $faction)
  {
    $units = Utils::filter($space->getUnits($faction), function ($unit) {
      return $unit->isReduced() && !$unit->isIndian();
    });

    $data = [
      ARTILLERY => [],
      FLEET => [],
      NON_INDIAN_LIGHT => [],
      METROPOLITAN_BRIGADES => [],
      NON_METROPOLITAN_BRIGADES => [],
    ];

    foreach ($units as $unit) {
      if ($unit->isArtillery()) {
        $data[ARTILLERY][] = $unit;
      } else if ($unit->isFleet()) {
        $data[FLEET][] = $unit;
      } else if ($unit->isNonIndianLight()) {
        $data[NON_INDIAN_LIGHT][] = $unit;
      } else if ($unit->isMetropolitanBrigade()) {
        $data[METROPOLITAN_BRIGADES][] = $unit;
      } else if ($unit->isNonMetropolitanBrigade()) {
        $data[NON_METROPOLITAN_BRIGADES][] = $unit;
      }
    }

    return $data;
  }
}
