<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;

class StackActions
{
  // Mapping of opId and corresponding class
  static $stackActions = [
    LIGHT_MOVEMENT => 'LightMovement',
    RAID => 'Raid',
  ];

  public static function get($stackActionId)
  {
    if (!\array_key_exists($stackActionId, self::$stackActions)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an action not defined in StackActions.php : ' . $stackActionId);
    }
    $name = '\BayonetsAndTomahawks\StackActions\\' . self::$stackActions[$stackActionId];
    return new $name();
  }
}
