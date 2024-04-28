<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;

class ActionPoints
{
  // Mapping of opId and corresponding class
  static $actionPoints = [
    INDIAN_AP => 'IndianAP',
    LIGHT_AP => 'LightAP',
  ];

  public static function get($actionPointId)
  {
    if (!\array_key_exists($actionPointId, self::$actionPoints)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an actionPoint not defined in ActionPoints.php : ' . $actionPointId);
    }
    $name = '\BayonetsAndTomahawks\ActionPoints\\' . self::$actionPoints[$actionPointId];
    return new $name();
  }
}
