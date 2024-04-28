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
use BayonetsAndTomahawks\Managers\StackActions;
use BayonetsAndTomahawks\Managers\Tokens;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class MovementLight extends \BayonetsAndTomahawks\Actions\MovementSelectDestinationAndUnits
{
  public function getState()
  {
    return ST_MOVEMENT_LIGHT;
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

  public function stMovementLight()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreMovementLight()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsMovementLight()
  {
    $info = $this->ctx->getInfo();
    $parentInfo = $this->ctx->getParent()->getInfo();

    $player = self::getPlayer();

    $stackActionId = $parentInfo['stackAction'];
    $stackAction = StackActions::get($stackActionId);

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

  public function actPassMovementLight()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actMovementLight($args)
  {
    self::checkAction('actMovementLight');

    Notifications::log('args', $args);
    $unitIds = $args['unitIds'];
    $spaceId = $args['spaceId'];
    $space = Spaces::get($spaceId);

    $stateArgs = $this->argsMovementLight();
    if (!isset($stateArgs['destinations'][$spaceId])) {
      throw new \feException("ERROR 001");
    }
    // TODO: check how to do this mnre efficiently
    $units = array_map(function ($unitId) {
      return Units::get($unitId);
    }, $unitIds); 
    Notifications::log('units',$units);
    $stateArgsUnitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $stateArgs['lightUnits']);
    Notifications::log('stateArgsUnitIds',$stateArgsUnitIds);
    $hasNotAllowedUnit = Utils::array_some($units, function ($unit) use ($stateArgsUnitIds) {
      return !in_array($unit->getId(), $stateArgsUnitIds);
    });
    if ($hasNotAllowedUnit) {
      throw new \feException("ERROR 002");
    }
    
    Units::move($unitIds, $spaceId);
    Notifications::moveStack(self::getPlayer(), $units, $stateArgs['origin'], $space);
    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
