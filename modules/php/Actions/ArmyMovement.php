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

class ArmyMovement extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_ARMY_MOVEMENT;
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

  public function stArmyMovement()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreArmyMovement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsArmyMovement()
  {
    $info = $this->ctx->getInfo();
    // $parent = $this->ctx->getParent();
    // $parentInfo = $parent->getInfo();
    // $actionPointId = $parent->getParent()->getInfo()['actionPointId'];

    // $resolved = $parent->getResolvedActions([ARMY_MOVEMENT]);
    $player = self::getPlayer();

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();
    // $unitsOnSpace = $space->getUnits($playerFaction);
    // $adjacentSpaces = $space->getAdjacentSpaces();

    $units = $this->getUnitsThatCanMove();

    // $units = [];

    // foreach ($unitsOnSpace as $unit) {
    //   // TODO filter units that cannot move
    //   if ($unit->getType() === FORT || $unit->getType() === BASTION) {
    //     continue;
    //   }

    //   $units[] = $unit;
    // }

    // $destinations = [];

    // foreach ($adjacentSpaces as $targetSpaceId => $connection) {
    //   $remainingConnectionLimit = $connection->getLimit() - $connection->getLimitUsed($playerFaction);

    //   // TODO: add other checks
    //   if ($remainingConnectionLimit > 0) {
    //     $destinations[$targetSpaceId] = [
    //       'space' => Spaces::get($targetSpaceId),
    //       'connection' => $connection,
    //       'remainingConnectionLimit' => $remainingConnectionLimit,
    //     ];
    //   }
    // }

    return [
      'units' => $units,
      'origin' => $space,
      // 'destinations' => $destinations,
      'faction' => $playerFaction,
      // 'numberResolved' => count($resolved),
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

  public function actPassArmyMovement()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actArmyMovement($args)
  {
    self::checkAction('actArmyMovement');

    $unitIds = $args['unitIds'];
    // $destinationId = $args['spaceId'];
    // $destination = Spaces::get($destinationId);
    Notifications::log('args', $args);
    $player = self::getPlayer();

    $allowedUnits = $this->getUnitsThatCanMove();


    // // TODO: check how to do this more efficiently
    $selectedUnits = array_map(function ($unitId) {
      return Units::get($unitId);
    }, $unitIds);
    // // Notifications::log('units', $units);
    $allowedUnitsIds = array_map(function ($unit) {
      return $unit->getId();
    }, $allowedUnits);
    // // Notifications::log('stateArgsUnitIds', $stateArgsUnitIds);
    $hasNotAllowedUnit = Utils::array_some($selectedUnits, function ($unit) use ($allowedUnitsIds) {
      return !in_array($unit->getId(), $allowedUnitsIds);
    });
    if ($hasNotAllowedUnit) {
      throw new \feException("ERROR 008");
    }

    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => ARMY_MOVEMENT_DESTINATION,
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

  public function getUnitsThatCanMove()
  {
    $info = $this->ctx->getInfo();
    $player = self::getPlayer();

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();

    $unitsOnSpace = $space->getUnits($playerFaction);
    $units = [];

    foreach ($unitsOnSpace as $unit) {
      // TODO filter units that cannot move
      if ($unit->getType() === FORT || $unit->getType() === BASTION) {
        continue;
      }

      $units[] = $unit;
    }

    return $units;
  }

  public function getUiData()
  {
    return [
      'id' => ARMY_MOVEMENT,
      'name' => clienttranslate("Army Movement"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    return count($units) > 0 && Utils::array_some($units, function ($unit) {
      return $unit->getType() !== FORT && $unit->getType() !== BASTION;
    });
  }

  public function getFlow($playerId, $originId)
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => ARMY_MOVEMENT,
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
