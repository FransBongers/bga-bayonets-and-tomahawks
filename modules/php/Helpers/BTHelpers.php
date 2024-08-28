<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;

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
    if ($marker->getState() > 0) {
      $value += 10 * $marker->getState();
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
}
