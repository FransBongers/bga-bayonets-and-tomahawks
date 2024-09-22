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
  public static function updateControl($player, $space)
  {
    $faction = $player->getFaction();

    $space->setControl($faction);
    Notifications::takeControl($player, $space);

    if ($space->getVictorySpace()) {
      Players::scoreVictoryPoints($player, $space->getValue());
    }
  }

  public static function getFriendlySeaZones($faction)
  {
    if ($faction === FRENCH) {
      return [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    }
    $openSeasMarker = Markers::get(OPEN_SEAS_MARKER);
    if ($openSeasMarker->getSide() === 0) {
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

    $counterIdMap = [
      BRITISH => [
        CHEROKEE => BRITISH_CHEROKEE,
        IROQUOIS => BRITISH_IROQUOIS,
      ],
      FRENCH => [
        CHEROKEE => FRENCH_CHEROKEE,
        IROQUOIS => FRENCH_IROQUOIS,
      ],
    ];

    $units = Utils::filter(Units::getInLocation(POOL_NEUTRAL_INDIANS)->toArray(), function ($unit) use ($indianNation, $faction, $counterIdMap) {
      return $unit->getCounterId() === $counterIdMap[$faction][$indianNation];
    });
    $indianNationVillages = $units[0]->getVillages();

    foreach ($units as $index => $unit) {
      $space = Spaces::get($indianNationVillages[$index]);
      $space->setControlStartOfTurn($faction);
      $space->setControl($faction);
      $unit->setLocation($space->getId());
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
      $marker = Markers::getMarkersFromSupply($type)[0];
      $marker->setLocation($markerLocation);

      Notifications::placeStackMarker($player, [$marker], $space);
    }
  }

  public static function getMarkersOnMap($type, $faction)
  {
    $markers = Markers::getMarkersOfType($type);
    return Utils::filter($markers, function ($marker) use ($faction) {
      $location = $marker->getLocation();
      return !Utils::startsWith($location, 'supply') && explode('_', $location)[1] === $faction;
    });
  }

  public static function factionOutnumbersEnemyInSpace($space, $faction)
  {

    $units = $space->getUnits();

    $enemyHasFort = false;
    $enemyUnits = [];
    $playerUnits = [];

    foreach ($units as $unit) {
      if ($unit->getType() === COMMANDER) {
        continue;
      }
      if ($unit->getFaction() === $faction) {
        $playerUnits[] = $unit;
      } else {
        $enemyUnits[] = $unit;
        if ($unit->getType() === FORT) {
          $enemyHasFort = true;
        }
      }
    }

    $enemyHasBastion = $faction === BRITISH && $space->hasBastion();
    $militia = $space->getHomeSpace() !== $faction ? $space->getMilitia() : 0;
    $numberOfEnemyUnits = count($enemyUnits) + $militia;

    $hasEnemyUnits = $numberOfEnemyUnits > 0;

    return [
      'hasEnemyUnits' => $hasEnemyUnits,
      'outnumbers' => $hasEnemyUnits && count($playerUnits) / $numberOfEnemyUnits > 3,
      'enemyHasBastion' => $enemyHasBastion,
      'enemyHasFort' => $enemyHasFort,
    ];
  }

  public static function getStacks($spaces = null, $units = null)
  {
    $spaces = $spaces === null ? Spaces::getAll() : $spaces;
    $units = $units === null ? Units::getAll() : $units;

    $stacks = [
      BRITISH => [],
      FRENCH => [],
    ];

    foreach ($units as $unit) {
      $location = $unit->getLocation();
      if (!(in_array($location, SPACES) && !in_array($location, BASTIONS))) {
        continue;
      }
      $faction = $unit->getFaction();
      // location is a Space
      if (isset($stacks[$faction][$location])) {
        $stacks[$faction][$location]['units'][] = $unit;
      } else {
        $stacks[$faction][$location] = [
          'units' => [$unit],
          'space' => $spaces[$location]
        ];
      }
    }

    return $stacks;
  }

  public static function lastEliminatedUnitCheck($player, $spaceId, $faction)
  {
    if (!BTHelpers::isSpace($spaceId)) {
      return;
    }

    $space = Spaces::get($spaceId);

    $units = $space->getUnits($faction);

    $hasNonCommanderUnit = Utils::array_some($units, function ($unit) {
      return !$unit->isCommander();
    });
    if ($hasNonCommanderUnit) {
      return;
    }
    $commanders = Utils::filter($units, function ($unit) {
      return $unit->isCommander();
    });
    foreach ($commanders as $commander) {
      $commander->eliminate($player);
    }

    $stackMarkers = Markers::getInLocation(Locations::stackMarker($spaceId, $faction));
    foreach($stackMarkers as $marker) {
      $marker->remove($player);
    }
  }
}
