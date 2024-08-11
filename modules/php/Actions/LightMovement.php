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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class LightMovement extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_LIGHT_MOVEMENT;
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

  public function stLightMovement()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreLightMovement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsLightMovement()
  {
    $info = $this->ctx->getInfo();
    $parent = $this->ctx->getParent();
    $parentInfo = $parent->getInfo();
    $actionPointId = $parent->getParent()->getInfo()['actionPointId'];

    $resolved = $parent->getResolvedActions([LIGHT_MOVEMENT]);
    $player = self::getPlayer();

    $isIndianActionPoint = $actionPointId === INDIAN_AP || $actionPointId === INDIAN_AP_2X;

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();
    $units = $space->getUnits($playerFaction);

    $commanders = [];
    $lightUnits = [];

    foreach ($units as $unit) {
      $unitType = $unit->getType();

      if ($isIndianActionPoint) {
        if ($unit->isIndian() && $unitType === LIGHT) {
          $lightUnits[] = $unit;
        }
        continue;
      } else if ($unitType === LIGHT) {
        $lightUnits[] = $unit;
      } else if ($unitType === COMMANDER) {
        $commanders[] = $unit;
      }
    }

    return [
      'isIndianAP' => $actionPointId === INDIAN_AP || $actionPointId === INDIAN_AP_2X,
      'commanders' => $commanders,
      'lightUnits' => $lightUnits,
      'origin' => $space,
      'faction' => $playerFaction,
      'numberResolved' => count($resolved),
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

  public function actPassLightMovement()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actLightMovement($args)
  {
    self::checkAction('actLightMovement');

    $unitIds = $args['unitIds'];
     Notifications::log('args', $args);
    $player = self::getPlayer();

    $stateArgs = $this->argsLightMovement();

    // TODO: check how to do this more efficiently
    $units = array_map(function ($unitId) {
      return Units::get($unitId);
    }, $unitIds);

    $stateArgsUnitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $stateArgs['lightUnits']);

    $hasNotAllowedUnit = Utils::array_some($units, function ($unit) use ($stateArgsUnitIds) {
      return !in_array($unit->getId(), $stateArgsUnitIds);
    });
    if ($hasNotAllowedUnit) {
      throw new \feException("ERROR 002");
    }


    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => LIGHT_MOVEMENT_DESTINATION,
      'spaceId' => $this->ctx->getInfo()['spaceId'],
      'playerId' => $player->getId(),
      'unitIds' => $unitIds,
    ]));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getUiData()
  {
    return [
      'id' => LIGHT_MOVEMENT,
      'name' => clienttranslate("Light Movement"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    $hasLightUnit = Utils::array_some($units, function ($unit) {
      // Notifications::log('unit', $unit);
      $unitType = $unit->getType();
      // Notifications::log('unitType', $unitType);
      return $unitType === LIGHT;
      // TODO: unit may not have moved already?
      // Battle?
    });
    // Notifications::log('LightMovement canBePerformedBy', $hasLightUnit);
    return $hasLightUnit;
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
      // 'stackAction' => LIGHT_MOVEMENT,
      // 'indianActionPoint' => $indianActionPoint,
      'originId' => $originId,
      'children' => [
        [
          'action' => LIGHT_MOVEMENT,
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
