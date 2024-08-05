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
}
