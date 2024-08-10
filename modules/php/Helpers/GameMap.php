<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class GameMap extends \APP_DbObject
{
  public static function getFriendlySeaZones($faction)
  {
    if ($faction === FRENCH) {
      return [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    }
    $openSeasMarker = Markers::get(OPEN_SEAS_MARKER);
    if ($openSeasMarker->getState() === 0) {
      return [ATLANTIC_OCEAN];
    } else {
      return [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    }
  }

  public static function performIndianNationControlProcedure($indianNation, $faction)
  {
    if ($indianNation === CHEROKEE) {
      Globals::setControlCherokee($faction);
    } else {
      Globals::setControlIroquois($faction);
    }

    $player = Players::getPlayerForFaction($faction);
    Notifications::indianNationControl($player, $indianNation, $faction);


    $units = Utils::filter(Units::getInLocation(POOL_NEUTRAL_INDIANS)->toArray(), function ($unit) use ($indianNation) {
      return $unit->getCounterId() === $indianNation;
    });
    $indianNationVillages = $units[0]->getVillages();

    foreach ($units as $index => $unit) {
      $space = Spaces::get($indianNationVillages[$index]);
      $unit->setLocation($space->getId());
      if ($faction === BRITISH) {
        $unit->setState(1);
      }
      Notifications::placeUnits($player, [$unit], $space, $faction);
    }
  }

  public static function awardRaidPoints($player, $faction, $points)
  {
    // Award raid points
    $raidMarker = Markers::get($faction === BRITISH ? BRITISH_RAID_MARKER : FRENCH_RAID_MARKER);
    $position = intval(explode('_', $raidMarker->getLocation())[2]);
    $newPosition = $position + $points;
    if ($newPosition < 0) {
      // Possible when losing raid points
      $raidMarker->setLocation(RAID_TRACK_0);
      Notifications::moveRaidPointsMarker($raidMarker);
    } else if ($newPosition < 8) {
      $raidMarker->setLocation(Locations::raidTrack($newPosition));
      Notifications::moveRaidPointsMarker($raidMarker);
    } else {
      $remainingRaidPoints = $newPosition - 8;
      $raidMarker->setLocation(RAID_TRACK_8);
      Notifications::moveRaidPointsMarker($raidMarker);

      Players::scoreVictoryPoints($player, 1);

      $raidMarker->setLocation(Locations::raidTrack($remainingRaidPoints));
      Notifications::moveRaidPointsMarker($raidMarker);
    }
  }

  /**
   * Places marker on a stack if the stack does not have that marker yet
   */
  public static function placeMarkerOnStack($player, $type, $space, $faction)
  {
    $markerLocation = Locations::stackMarker($space->getId(), $faction);
    $existingMarker = Markers::getOfTypeInLocation($type, $markerLocation);
    if (count($existingMarker) === 0) {
      $marker = Markers::getMarkerFromSupply($type);
      $marker->setLocation($markerLocation);

      Notifications::placeStackMarker($player, $marker, $space);
    }
  }

  public static function getMarkersOnMap($type, $faction) {
    $markers = Markers::getMarkersOfType($type);
    return Utils::filter($markers, function ($marker) use ($faction) {
      $location = $marker->getLocation();
      return !Utils::startsWith($location,'supply') && explode('_', $location)[1] === $faction;
    });
  }
}
