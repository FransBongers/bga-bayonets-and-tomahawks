<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;

class ActionPoints
{
  // Mapping of opId and corresponding class
  static $actionPoints = [
    ARMY_AP => 'ArmyAP',
    ARMY_AP_2X => 'ArmyAP_2x',
    INDIAN_AP => 'IndianAP',
    INDIAN_AP_2X => 'IndianAP_2x',
    FRENCH_LIGHT_ARMY_AP => 'FrenchLightArmyAP',
    LIGHT_AP => 'LightAP',
    LIGHT_AP_2X => 'LightAP_2x',
    SAIL_ARMY_AP => 'SailArmyAP',
    SAIL_ARMY_AP_2X => 'SailArmyAP_2x',
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
