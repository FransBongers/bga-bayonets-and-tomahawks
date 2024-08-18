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
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class MoveStack extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MOVE_STACK;
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

  public function stMoveStack()
  {
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();

    $info = $this->ctx->getInfo();

    $originId = $info['fromSpaceId'];
    $destinationId = $info['toSpaceId'];
    $unitIds = $info['unitIds'];
    $connectionId = isset($info['connectionId']) ? $info['connectionId'] : null;

    $units = Units::getMany($unitIds)->toArray();
    $destination = $destinationId !== SAIL_BOX ? Spaces::get($destinationId) : null;
    $origin = $originId !== SAIL_BOX ? Spaces::get($originId) : null;
    $connection = $connectionId !== null ? Connections::get($connectionId) : null;

    $destinationUnits = [];
    if ($destinationId === SAIL_BOX) {
      $destinationUnits = Utils::filter(Units::getInLocation(SAIL_BOX)->toArray(), function ($unit) use ($playerFaction) {
        return $unit->getFaction() === $playerFaction;
      });
    } else if ($destination !== null) {
      $destinationUnits = $destination->getUnits($playerFaction);
    }

    $originUnits = [];
    if ($originId === SAIL_BOX) {
      $originUnits = Utils::filter(Units::getInLocation(SAIL_BOX)->toArray(), function ($unit) use ($playerFaction) {
        return $unit->getFaction() === $playerFaction;
      });
    } else if ($originId !== null) {
      $originUnits = $origin->getUnits($playerFaction);
    }

    // Update markers
    $destinationHasUnits = count($destinationUnits) > 0;
    $unitsRemainInOrigin = Utils::array_some($originUnits, function ($unit) use ($unitIds) {
      return !in_array($unit->getId(), $unitIds);
    });

    $destinationMarkers = Markers::getInLocation(Locations::stackMarker($destinationId, $playerFaction))->toArray();
    $originMarkers = Markers::getInLocation(Locations::stackMarker($originId, $playerFaction))->toArray();

    $movedMarkers = [];
    $createInOrigin = [];
    $removeFromDestination = [];
    /**
     * Remove marker if:
     * - destination already has units with marker
     * - destination has units without marker
     * - Unless units remain
     */
    foreach ([OUT_OF_SUPPLY_MARKER, ROUT_MARKER] as $markerType) {
      $destinationMarker = Utils::array_find($destinationMarkers, function ($marker) use ($markerType) {
        return Utils::startsWith($marker->getId(), $markerType);
      });
      $originMarker = Utils::array_find($originMarkers, function ($marker) use ($markerType) {
        return Utils::startsWith($marker->getId(), $markerType);
      });


      if ($originMarker !== null && $destinationHasUnits && !$unitsRemainInOrigin) {
        // Remove if destination does not have the marker (ie, stack joins a unit without marker)
        $originMarker->remove($player);
      } else if ($originMarker !== null) {
        // Move marker
        $originMarker->setLocation(Locations::stackMarker($destinationId, $playerFaction));
        $movedMarkers[] = $originMarker;

        // Create if marker is moved and units remaing in origin
        if ($unitsRemainInOrigin) {
          $createInOrigin[] = $markerType;
        }
      }

      // Remove in destination if destination has a marker and is joined by a stack
      // who does not have a marker 
      if ($originMarker === null && $destinationMarker !== null) {
        $removeFromDestination[] = $destinationMarker;
      }
    }

    Units::move($unitIds, $destinationId, null, $originId);

    // Update connection limit
    if ($connection !== null) {
      $connectionLimitIncrease = count(Utils::filter($units, function ($unit) {
        return !$unit->isCommander() && !$unit->isFleet();
      }));
      $connection->incLimitUsed($playerFaction, $connectionLimitIncrease);
    }

    Notifications::moveStack($player, $units, $movedMarkers, $origin, $destination, $connection, false, $destinationId === SAIL_BOX || $originId === SAIL_BOX);

    // Add markers to remaining units
    foreach ($createInOrigin as $markerType) {
      GameMap::placeMarkerOnStack($player, $markerType, $origin, $playerFaction);
    }
    if ($originId === SAIL_BOX) {
      GameMap::placeMarkerOnStack($player, LANDING_MARKER, $destination, $playerFaction);
    }

    foreach ($removeFromDestination as $marker) {
      $marker->remove($player);
    }

    $this->resolveAction(['automatic' => true, 'unitIds' => $unitIds]);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
