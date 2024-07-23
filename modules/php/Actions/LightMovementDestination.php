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

    $player = self::getPlayer();

    // $isIndianActionPoint = $actionPointId === INDIAN_AP || $actionPointId === INDIAN_AP_2X;

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();
    // $units = $space->getUnits($playerFaction);
    $adjacentSpaces = $space->getAdjacentConnections();
    $unitIds = $info['unitIds'];

    $units = Units::getMany($unitIds)->toArray();

    // $commanders = [];
    // $lightUnits = [];

    // foreach ($units as $unit) {
    //   $unitType = $unit->getType();

    //   if ($isIndianActionPoint) {
    //     if ($unit->isIndian() && $unitType === LIGHT) {
    //       $lightUnits[] = $unit;
    //     }
    //     continue;
    //   } else if ($unitType === LIGHT) {
    //     $lightUnits[] = $unit;
    //   } else if ($unitType === COMMANDER) {
    //     $commanders[] = $unit;
    //   }
    // }

    $destinations = [];

    foreach ($adjacentSpaces as $targetSpaceId => $connection) {
      $remainingConnectionLimit = $connection->getLimit() - $connection->getLimitUsed($playerFaction);
      // TODO: add other checks
      // TODO: commanders
      if ($remainingConnectionLimit < count($unitIds)) {
        continue;
      }

      $destinations[$targetSpaceId] = [
        'space' => Spaces::get($targetSpaceId),
        'connection' => $connection,
      ];
    }

    return [
      'units' => $units,
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

  public function actPassLightMovementDestination()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actLightMovementDestination($args)
  {
    self::checkAction('actLightMovementDestination');

    // $unitIds = $args['unitIds'];
    $destinationId = $args['spaceId'];
    $destination = Spaces::get($destinationId);
    Notifications::log('args', $args);
    $player = self::getPlayer();

    $stateArgs = $this->argsLightMovementDestination();
    if (!isset($stateArgs['destinations'][$destinationId])) {
      throw new \feException("ERROR 001");
    }

    $info = $this->ctx->getInfo();
    $origin = Spaces::get($info['spaceId']);
    $unitIds = $info['unitIds'];
    $units = Units::getMany($unitIds)->toArray();

    Units::move($unitIds, $destinationId);

    // TODO: move markers
    Notifications::moveStack(self::getPlayer(), $units, [], $origin, $destination);

    // Check if origin was Settled space and is now empty of enemy units
    $this->loseControlCheck($player, $origin);

    // Check if players takes control of empty enemy controlled outpost
    $this->takeControlCheck($player, $destination);

    $enemyUnitsAndOverwhelm = $this->checkEnemyUnitsAndOverwhelm($destination, $player);

    $battleOccurs = $enemyUnitsAndOverwhelm['battleOccurs'];

    $resolvedMoves = count($this->ctx->getParent()->getResolvedActions([LIGHT_MOVEMENT]));
    if (!$battleOccurs && $resolvedMoves < 3) {
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
          'optional' => true,
        ],
      ],
    ];
  }
}
