<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class BTHelpers extends \APP_DbObject
{
  public static function getYear()
  {
    $yearMarker = Markers::get(YEAR_MARKER);
    $year = intval(explode('_', $yearMarker->getLocation())[2]);
    return $year;
  }

  public static function getAvailableActionPoints($usedActionPoints, $card, $addedActionPoints)
  {
    $cardActionPoints = array_merge($card->getActionPoints(), $addedActionPoints);

    $result = [];
    foreach ($cardActionPoints as $cIndex => $actionPoint) {
      $uIndex = Utils::array_find_index($usedActionPoints, function ($uActionPointId) use ($actionPoint) {
        return $uActionPointId === $actionPoint['id'];
      });
      if ($uIndex === null) {
        $result[] = $actionPoint;
      } else {
        unset($usedActionPoints[$uIndex]);
        $usedActionPoints = array_values($usedActionPoints);
      }
    }

    return $result;
  }

  public static function getOtherFaction($faction)
  {
    return $faction === BRITISH ? FRENCH : BRITISH;
  }

  private static function getBattleMarker($faction)
  {
    return Markers::get(FACTION_BATTLE_MARKER_MAP[$faction]);
  }

  private static function getBattleMarkerValue($marker)
  {
    $location = $marker->getLocation();
    $split = explode('_', $location);

    $value = intval($split[4]);
    if ($split[3] === 'minus') {
      $value = $value * -1;
    }
    if ($marker->getSide() > 0) {
      $value += 10 * $marker->getSide();
    }
    return $value;
  }

  public static function moveBattleVictoryMarker($player, $faction, $positions = 1)
  {
    $marker = self::getBattleMarker($faction);
    $value = self::getBattleMarkerValue($marker);

    $value += $positions;
    if ($value > 10) {
      $marker->setSide(floor($value / 10));
    }
    $isAttacker = explode('_', $marker->getLocation())[2] === 'attacker';

    $marker->setLocation(Locations::battleTrack($isAttacker, $value));
    Notifications::moveBattleVictoryMarker($player, $marker, $positions);
    return $value;
  }

  public static function isSpace($spaceId) {
    return in_array($spaceId, SPACES) && !in_array($spaceId, BASTIONS);
  }

  public static function returnSpaceIds($spaces)
  {
    return array_map(function ($space) {
      return $space->getId();
    }, $spaces);
  }

  public static function getSpacesBasedOnFleetRetreatPriorities($faction)
  {
    $spaces = Spaces::getAll()->toArray();
    $units = Units::getAll()->toArray();

    $coastalSpacesFreeOfEnemyUnits = Utils::filter($spaces, function ($space) use ($units, $faction) {
      if (!$space->isCoastal() || $space->getControl() === BTHelpers::getOtherFaction($faction)) {
        return false;
      }
      $spaceId = $space->getId();
      return !Utils::array_some($units, function ($unit) use ($spaceId, $faction) {
        return $unit->getLocation() === $spaceId && $unit->getFaction() !== $faction;
      });
    });

    // 1. Friendly Coastal Home Space
    $friendlyCoastalHomeSpaces = Utils::filter($coastalSpacesFreeOfEnemyUnits, function ($space) use ($faction) {
      return $space->getHomeSpace() !== null && $space->getControl() === $faction;
    });
    if (count($friendlyCoastalHomeSpaces) > 0) {
      return self::returnSpaceIds($friendlyCoastalHomeSpaces);
    }

    // Get coastal spaces of friendly sea zones
    $friendlySeaZones = GameMap::getFriendlySeaZones($faction);
    $coastalSpacesOfFriendlySZ = Utils::filter($coastalSpacesFreeOfEnemyUnits, function ($space) use ($friendlySeaZones) {
      return Utils::array_some($friendlySeaZones, function ($friendlySZ) use ($space) {
        return in_array($friendlySZ, $space->adjacentSeaZones());
      });
    });

    // 2. Frienldy Coastal Space of a friendly SZ
    $friendlyCoastalSpaceOfFriendlySZ = Utils::filter($coastalSpacesOfFriendlySZ, function ($space) use ($faction) {
      return $space->getControl() === $faction;
    });
    if (count($friendlyCoastalSpaceOfFriendlySZ) > 0) {
      return self::returnSpaceIds($friendlyCoastalSpaceOfFriendlySZ);
    }

    // 3, Wilderness Coastal Space of friendly SZ
    $wildernessCoastalSpaceOfFriendlySZ = Utils::filter($coastalSpacesOfFriendlySZ, function ($space) use ($faction) {
      return $space->getControl() === NEUTRAL;
    });
    if (count($wildernessCoastalSpaceOfFriendlySZ) > 0) {
      return self::returnSpaceIds($wildernessCoastalSpaceOfFriendlySZ);
    }

    // Return to Sail Box
    return [SAIL_BOX];
  }
}
