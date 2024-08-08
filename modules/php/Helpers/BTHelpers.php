<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Managers\Markers;

class BTHelpers extends \APP_DbObject
{
  public static function getYear()
  {
    $yearMarker = Markers::get(YEAR_MARKER);
    $year = intval(explode('_', $yearMarker->getLocation())[2]);
    return $year;
  }

  public static function getAvailableActionPoints($usedActionPoints, $card)
  {
    $cardActionPoints = $card->getActionPoints();

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
}
