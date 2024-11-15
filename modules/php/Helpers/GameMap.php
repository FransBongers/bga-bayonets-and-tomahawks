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

  /**
   * Returns minimum number of units required to overwhelm enemy
   * - returns 1000 if not possible to overwhelm
   */
  public static function requiredForOverwhelm($space, $faction, $unitsOnSpace)
  {
    // Enemy has Bastion
    if ($faction === BRITISH && $space->hasBastion()) {
      return [
        'requiredForOverwhelm' => 1000,
        'hasEnemyUnits' => true,
      ];
    };

    $enemyUnits = Utils::filter($unitsOnSpace, function ($unit) use ($faction) {
      return $unit->getFaction() !== $faction && !$unit->isCommander();
    });

    if (Utils::array_some($enemyUnits, function ($unit) {
      return $unit->isFort();
    })) {
      return [
        'requiredForOverwhelm' => 1000,
        'hasEnemyUnits' => true,
      ];
    }

    $friendlyUnits = Utils::filter($unitsOnSpace, function ($unit) use ($faction) {
      return $unit->getFaction() === $faction && !$unit->isCommander();
    });

    $enemyUnitCount = count($enemyUnits);
    $friendlyUnitCount = count($friendlyUnits);

    $militia = $space->getMilitia();

    if ($militia > 0 && $space->getHomeSpace() === $faction) {
      $friendlyUnitCount += $militia;
    } else if ($militia > 0) {
      $enemyUnitCount += $militia;
    }

    $minimumRequired = $enemyUnitCount * 3 + 1 - $friendlyUnitCount;

    return [
      'requiredForOverwhelm' => max($minimumRequired, 0),
      'hasEnemyUnits' => $enemyUnitCount > 0,
    ];
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
    $outnumbers = $hasEnemyUnits && count($playerUnits) / $numberOfEnemyUnits > 3;

    return [
      'hasEnemyUnits' => $hasEnemyUnits,
      'hasEnemyUnitsExcludingMilitia' => count($enemyUnits) > 0,
      'outnumbers' => $outnumbers,
      'enemyHasBastion' => $enemyHasBastion,
      'enemyHasFort' => $enemyHasFort,
      'overwhelm' => !($enemyHasFort || $enemyHasBastion) && $outnumbers,
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
    foreach ($stackMarkers as $marker) {
      $marker->remove($player);
    }
  }

  public static function loseControlCheck($player, $space)
  {
    $playerFaction = $player->getFaction();
    $otherFaction = BTHelpers::getOtherFaction($playerFaction);

    $playerLosesControl = $space->getControl() === $playerFaction && $space->isSettledSpace($otherFaction) &&

      count($space->getUnits($playerFaction)) === 0;

    if ($playerLosesControl) {
      $space->setControl($otherFaction);
      Notifications::loseControl($player, $space);

      if ($space->getVictorySpace()) {
        Players::scoreVictoryPoints($player, -1 * $space->getValue());
      }
    }
  }

  /**
   * $unitsPerSpace[$spaceId] = [
   * 'space' => $space,
   * 'units' => [$unit],
   * ];
   */
  public static function placeUnits($unitsPerSpace, $player, $faction)
  {
    $unitsPerSpace = array_values($unitsPerSpace);
    $markers = Markers::getAll()->toArray();
    
    usort($unitsPerSpace, function ($a, $b) {
      return $a['space']->getBattlePriority() - $b['space']->getBattlePriority();
    });

    foreach ($unitsPerSpace as $data) {
      $space = $data['space'];
      $units = $data['units'];
      Units::move(array_map(function ($unit) {
        return $unit->getId();
      }, $units), $space->getId());

      Notifications::placeUnits($player, $units, $space, $faction);
      $markers = Utils::filter($markers, function ($marker) use ($space) {
        return in_array($marker->getType(), [OUT_OF_SUPPLY_MARKER, ROUT_MARKER]) && Utils::startsWith($marker->getLocation(), $space->getId());
      });
      foreach ($markers as $marker) {
        $marker->remove($player);
      }
    }
  }

  /**
   * spaces: spaces controlled by faction
   * faction: faction to check Settled Space for
   */
  public static function controlsNumberOfSettledSpacesOfFaction($spaces, $faction, $number)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) use ($faction) {
      return $space->isSettledSpace($faction);
    });
    return count($countingSpaces) >= $number;
  }

  /**
   * spaces: spaces controlled by faction
   * faction: faction to check Victory Space for
   */
  public static function controlsNumberOfVictorySpacesOfFaction($spaces, $faction, $number)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) use ($faction) {
      return $space->isHomeSpace($faction) && $space->isVictorySpace();
    });
    return count($countingSpaces) >= $number;
  }

  /**
   * spaces: spaces controlled by faction
   * faction: faction to check Home Space for
   */
  public static function controlsNumberOfHomeSpacesOfFaction($spaces, $faction, $number)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) use ($faction) {
      return $space->isHomeSpace($faction);
    });
    return count($countingSpaces) >= $number;
  }
}
