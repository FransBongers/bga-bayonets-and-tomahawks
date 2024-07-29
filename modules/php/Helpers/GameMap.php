<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Managers\Markers;

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
}
