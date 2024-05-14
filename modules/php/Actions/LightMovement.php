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
    $resolved = $parent->getResolvedActions([LIGHT_MOVEMENT]);
    // Notifications::log('resolved', count($resolved));count($resolved)
    $player = self::getPlayer();

    // $stackActionId = $parentInfo['stackAction'];
    // $stackAction = StackActions::get($stackActionId);

    $indianActionPoint = $parentInfo['indianActionPoint'];

    $spaceId = $info['space'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();
    $units = $space->getUnits($playerFaction);
    $adjacentSpaces = $space->getAdjacentSpaces();

    $commanders = [];
    $lightUnits = [];

    foreach ($units as $unit) {
      $unitType = $unit->getType();

      if ($indianActionPoint) {
        if ($unit->isIndian() && $unitType === LIGHT) {
          $lightUnits[] = $unit;
        }
        continue;
      } else if ($unitType === LIGHT) {
        $lightUnits[] = $unit;
        continue;
      } else if ($unitType === COMMANDER) {
        $commanders[] = $unit;
        continue;
      }
    }

    $destinations = [];

    foreach ($adjacentSpaces as $targetSpaceId => $connection) {
      $remainingConnectionLimit = $connection->getLimit() - $connection->getLimitUsed($playerFaction);
      
      // TODO: add other checks
      if ($remainingConnectionLimit > 0) {
        $destinations[$targetSpaceId] = [
          'space' => Spaces::get($targetSpaceId),
          'remainingConnectionLimit' => $remainingConnectionLimit,
        ];
      }
    }

    return [
      // 'info' => $info,
      // 'parentInfo' => $parentInfo,
      'commanders' => $commanders,
      'lightUnits' => $lightUnits,
      'origin' => $space,
      'destinations' => $destinations,
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
    $spaceId = $args['spaceId'];
    $space = Spaces::get($spaceId);

    $stateArgs = $this->argsLightMovement();
    if (!isset($stateArgs['destinations'][$spaceId])) {
      throw new \feException("ERROR 001");
    }
    // TODO: check how to do this mnre efficiently
    $units = array_map(function ($unitId) {
      return Units::get($unitId);
    }, $unitIds);
    // Notifications::log('units', $units);
    $stateArgsUnitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $stateArgs['lightUnits']);
    // Notifications::log('stateArgsUnitIds', $stateArgsUnitIds);
    $hasNotAllowedUnit = Utils::array_some($units, function ($unit) use ($stateArgsUnitIds) {
      return !in_array($unit->getId(), $stateArgsUnitIds);
    });
    if ($hasNotAllowedUnit) {
      throw new \feException("ERROR 002");
    }

    Units::move($unitIds, $spaceId);
    Notifications::moveStack(self::getPlayer(), $units, $stateArgs['origin'], $space);

    $resolvedMoves = count($this->ctx->getParent()->getResolvedActions([LIGHT_MOVEMENT]));
    if ($resolvedMoves < 2) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => LIGHT_MOVEMENT,
        'space' => $spaceId,
        'playerId' => self::getPlayer()->getId(),
      ]));
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

  public function getFlow($playerId, $originId)
  {
    return [
      // 'stackAction' => LIGHT_MOVEMENT,
      // 'indianActionPoint' => $indianActionPoint,
      'originId' => $originId,
      'children' => [
        [
          'action' => LIGHT_MOVEMENT,
          'space' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
