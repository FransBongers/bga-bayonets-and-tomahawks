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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Movement extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MOVEMENT;
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

  public function stMovement()
  {
    $info = $this->ctx->getInfo();
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreMovement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsMovement()
  {
    $info = $this->ctx->getInfo();
    $actionPointId = $info['actionPointId'];

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $player = self::getPlayer();
    $playerFaction = $player->getFaction();

    $unitsOnSpace = $space->getUnits($playerFaction);
    $units = $this->getUnitsThatCanMove($space, $playerFaction, $unitsOnSpace, $actionPointId);

    $adjacent = $space->getAdjacentConnectionsAndSpaces();

    return [
      'actionPointId' => $actionPointId,
      'adjacent' => $adjacent,
      'fromSpace' => $space,
      'faction' => $playerFaction,
      'units' => $units,
      'count' => count($this->ctx->getParent()->getResolvedActions([MOVEMENT])),
    ];


    // // // $parent = $this->ctx->getParent();
    // // // $parentInfo = $parent->getInfo();
    // // // $actionPointId = $parent->getParent()->getInfo()['actionPointId'];

    // // // $resolved = $parent->getResolvedActions([ARMY_MOVEMENT]);

    // // 
    // $adjacentSpaces = $space->getAdjacentConnections();
    // $unitIds = $info['unitIds'];

    // $units = Units::getMany($unitIds)->toArray();

    // // foreach ($unitsOnSpace as $unit) {
    // //   // TODO filter units that cannot move
    // //   if ($unit->getType() === FORT || $unit->getType() === BASTION) {
    // //     continue;
    // //   }

    // //   $units[] = $unit;
    // // }

    // $destinations = [];

    // $requiresCoastalConnection = Utils::array_some($units, function ($unit) {
    //   return $unit->getType() === FLEET;
    // });

    // $requiresRoadOrHighway = Utils::array_some($units, function ($unit) {
    //   return in_array($unit->getType(), [ARTILLERY, BRIGADE, COMMANDER]);
    // });

    // foreach ($adjacentSpaces as $targetSpaceId => $connection) {
    //   if ($requiresRoadOrHighway && $connection->getType() === PATH) {
    //     continue;
    //   }
    //   if ($requiresCoastalConnection && !$connection->isCoastalConnection()) {
    //     continue;
    //   }

    //   $remainingConnectionLimit = $connection->getLimit() - $connection->getLimitUsed($playerFaction);

    //   if ($remainingConnectionLimit < count($unitIds)) {
    //     // TODO: check roads and artillery limit
    //     continue;
    //   }

    //   $destinations[$targetSpaceId] = [
    //     'space' => Spaces::get($targetSpaceId),
    //     'connection' => $connection,
    //   ];
    // }

    // return [
    //   'units' => $units,
    //   'destinations' => $destinations,
    //   'faction' => $playerFaction,
    // ];
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

  public function actPassMovement()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    // Engine::resolve(PASS);
    $this->resolveAction(PASS);
  }

  public function actMovement($args)
  {
    self::checkAction('actMovement');

    // $unitIds = $args['unitIds'];
    $destinationId = $args['destinationId'];
    $selectedUnitIds = $args['selectedUnitIds'];

    $stateArgs = $this->argsMovement();

    $adjacent = Utils::array_find($stateArgs['adjacent'], function ($adjacentOption) use ($destinationId) {
      return $adjacentOption['space']->getId() === $destinationId;
    });

    if ($adjacent === null) {
      throw new \feException("ERROR 050");
    }

    $units = Utils::filter($stateArgs['units'], function ($unit) use ($selectedUnitIds) {
      return in_array($unit->getId(), $selectedUnitIds);
    });

    if (count($units) !== count($selectedUnitIds)) {
      throw new \feException("ERROR 051");
    }

    // $destination = $adjacent['space'];

    $player = self::getPlayer();

    $info = $this->ctx->getInfo();
    $originId = $info['spaceId'];
    // $origin = Spaces::get($originId);

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);

    // $playerFaction = $player->getFaction();

    // // Update markers
    // $destinationHasUnits = count($destination->getUnits($playerFaction)) > 0;
    // $unitsRemainInOrigin = Utils::array_some($origin->getUnits($playerFaction), function ($unit) use ($selectedUnitIds) {
    //   return !in_array($unit->getId(), $selectedUnitIds);
    // });

    // $destinationMarkers = Markers::getInLocation(Locations::stackMarker($destinationId, $playerFaction))->toArray();
    // $originMarkers = Markers::getInLocation(Locations::stackMarker($originId, $playerFaction))->toArray();

    // $movedMarkers = [];
    // $createInOrigin = [];
    // $removeFromDestination = [];
    // /**
    //  * Remove marker if:
    //  * - destination already has units with marker
    //  * - destination has units without marker
    //  * -Unless units remain
    //  */
    // foreach ([OUT_OF_SUPPLY_MARKER, ROUT_MARKER] as $markerType) {
    //   $destinationMarker = Utils::array_find($destinationMarkers, function ($marker) use ($markerType) {
    //     return Utils::startsWith($marker->getId(), $markerType);
    //   });
    //   $originMarker = Utils::array_find($originMarkers, function ($marker) use ($markerType) {
    //     return Utils::startsWith($marker->getId(), $markerType);
    //   });


    //   if ($originMarker !== null && $destinationHasUnits && !$unitsRemainInOrigin) {
    //     // Remove if destination does not have the marker (ie, stack joins a unit without marker)
    //     $originMarker->remove($player);
    //   } else if ($originMarker !== null) {
    //     // Move marker
    //     $originMarker->setLocation(Locations::stackMarker($destinationId, $playerFaction));
    //     $movedMarkers[] = $originMarker;

    //     // Create if marker is moved and units remaing in origin
    //     if ($unitsRemainInOrigin) {
    //       $createInOrigin[] = $markerType;
    //     }
    //   }

    //   // Remove in destination if destination has a marker and is joined by a stack
    //   // who does not have a marker 
    //   if ($originMarker === null && $destinationMarker !== null) {
    //     $removeFromDestination[] = $destinationMarker;
    //   }
    // }

    // Units::move($unitIds, $destinationId, null, $originId);

    // // Update connection limit
    $connection = $adjacent['connection'];
    // $connectionLimitIncrease = count(Utils::filter($units, function ($unit) {
    //   return !$unit->isCommander() && !$unit->isFleet();
    // }));
    // $connection->incLimitUsed($playerFaction, $connectionLimitIncrease);


    // Notifications::moveStack($player, $units, $movedMarkers, $origin, $destination, $connection);

    // // Add markers to remaining units
    // foreach ($createInOrigin as $markerType) {
    //   GameMap::placeMarkerOnStack($player, $markerType, $origin, $playerFaction);
    // }

    // foreach ($removeFromDestination as $marker) {
    //   $marker->remove($player);
    // }

    $playerId = $player->getId();

    $this->ctx->insertAsBrother(Engine::buildTree([
      'children' => [
        [
          'action' => MOVEMENT_LOSE_CONTROL_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $originId,
        ],
        [
          'action' => MOVEMENT_OVERWHELM_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $destinationId,
        ],
        [
          'action' => MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK,
          'actionPointId' => $info['actionPointId'],
          'playerId' => $player->getId(),
          'spaceId' => $destinationId,
        ]
      ]
    ]));

    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => MOVE_STACK,
      'playerId' => $playerId,
      'fromSpaceId' => $originId,
      'toSpaceId' => $destinationId,
      'unitIds' => $unitIds,
      'connectionId' => $connection->getId(),
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

  public function getUnitsThatCanMove($space, $faction, $units, $actionPointId, $ignoreAlreadyMovedCheck = false)
  {
    $currentNumberOfMoves = 0;
    $mpMultiplier = 1;
    if (!$ignoreAlreadyMovedCheck) {
      $currentNumberOfMoves = count($this->ctx->getParent()->getResolvedActions([MOVEMENT]));
      $mpMultiplier = in_array($this->ctx->getInfo()['actionPointId'], [ARMY_AP_2X, LIGHT_AP_2X, INDIAN_AP_2X, SAIL_ARMY_AP_2X]) ? 2 : 1;
    }

    $battleInSpace = $space->getBattle() === 1;

    if ($battleInSpace) {
      $data = GameMap::factionOutnumbersEnemyInSpace($space, $faction);
      if (!$data['outnumbers'] && !($faction === BRITISH && $data['enemyHasBastion'])) {
        return [];
      }
    }

    // TODO: filter units that are locked in battle?
    $unitsThatCanMove = Utils::filter($units, function ($unit) use ($ignoreAlreadyMovedCheck, $currentNumberOfMoves, $mpMultiplier) {
      if ($unit->isFort() || $unit->isBastion()) {
        return false;
      }
      if (!$ignoreAlreadyMovedCheck && $currentNumberOfMoves >= $unit->getMpLimit() * $mpMultiplier) {
        return false;
      }
      return !$unit->isSpent();
    });

    if (in_array($actionPointId, [INDIAN_AP, INDIAN_AP_2X])) {
      return Utils::filter($unitsThatCanMove, function ($unit) {
        return $unit->isIndian();
      });
    }
    if (in_array($actionPointId, [LIGHT_AP, LIGHT_AP_2X])) {
      return Utils::filter($unitsThatCanMove, function ($unit) {
        return $unit->isLight();
      });
    }
    return $unitsThatCanMove;
  }

  public function getUiData()
  {
    return [
      'id' => MOVEMENT,
      'name' => clienttranslate("Movement"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    return count($this->getUnitsThatCanMove($space, $playerFaction, $units, $actionPoint->getId(), true)) > 0;
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => MOVEMENT,
          'spaceId' => $originId,
          'actionPointId' => $actionPointId,
          'playerId' => $playerId,
        ],
        [
          'action' => MOVEMENT_PLACE_SPENT_MARKERS,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
