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

class LightMovementDestination extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_LIGHT_MOVEMENT_DESTINATION;
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

  public function stLightMovementDestination()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreLightMovementDestination()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsLightMovementDestination()
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
    $adjacentSpaces = $space->getAdjacentSpaces();

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
      'info' => $info,
      'actionPointId' => $actionPointId,
      'parentInfo' => $parentInfo,
      'parentParentInfo' => $parent->getParent()->getInfo(),
      'isIndianAP' => $actionPointId === INDIAN_AP || $actionPointId === INDIAN_AP_2X,
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

  public function actPassLightMovementDestination()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actLightMovementDestination($args)
  {
    self::checkAction('actLightMovementDestination');

    $unitIds = $args['unitIds'];
    $destinationId = $args['spaceId'];
    $destination = Spaces::get($destinationId);
    Notifications::log('args', $args);
    $player = self::getPlayer();

    $stateArgs = $this->argsLightMovementDestination();
    if (!isset($stateArgs['destinations'][$destinationId])) {
      throw new \feException("ERROR 001");
    }
    // TODO: check how to do this more efficiently
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

    $origin = $stateArgs['origin'];

    Units::move($unitIds, $destinationId);

    Notifications::moveStack(self::getPlayer(), $units, $origin, $destination);

    // Check if origin was Settled space and is now empty of enemy units
    $this->loseControlCheck($player, $origin);

    // Check if players takes control of empty enemy controlled outpost
    $this->takeControlCheck($player, $destination);

    $enemyUnitsAndOverwhelm = $this->checkEnemyUnitsAndOverwhelm($destination, $player);
    // Notifications::log('enemyUnitsAndOverwhelm', $enemyUnitsAndOverwhelm);
    // $hasEnemyUnits = $enemyUnitsAndOverwhelm['hasEnemyUnits'];
    // $overwhelm = $enemyUnitsAndOverwhelm['overwhelm'];
    $battleOccurs = $enemyUnitsAndOverwhelm['battleOccurs'];

    // $battle = $hasEnemyUnits && !$overwhelm;
    // Add battle marker
    

    $resolvedMoves = count($this->ctx->getParent()->getResolvedActions([LIGHT_MOVEMENT]));
    if (!$battleOccurs && $resolvedMoves < 2) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => LIGHT_MOVEMENT,
        'spaceId' => $destinationId,
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
    // Notifications::log('LightMovementDestination canBePerformedBy', $hasLightUnit);
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
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
