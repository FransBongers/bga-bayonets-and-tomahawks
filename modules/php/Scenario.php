<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Preferences;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Units;

class Scenario extends \APP_DbObject
{
  protected static $scenario = null;
  public static function get()
  {
    if (self::$scenario == null) {
      // Not sure why but using Globals module truncate some part of the query and makes it unusable (at least the last time I checked)
      $scenario = self::getUniqueValueFromDB("SELECT value FROM global_variables WHERE name = 'scenario' LIMIT 1");
      self::$scenario = is_null($scenario) ? null : json_decode($scenario, true);
    }
    return self::$scenario;
  }

  public function getId()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['meta_data']['scenario_id'] ?? $scenario['meta_data']['id'];
  }

  /**
   * Load a scenario from a file and store it into a global
   */
  function loadId($id)
  {
    require_once dirname(__FILE__) . '/Scenarios/list.inc.php';
    $scenarios = [];

    if (isset($scenariosMap[$id])) {
      $name = $scenariosMap[$id];
      require_once dirname(__FILE__) . '/Scenarios/' . $name . '.php';
    }
    $scenario = $scenarios[$id];


    self::$scenario = $scenario;
    Globals::setScenario($scenario);
  }


  /**
   * Setup the scenario stored into the global
   */
  function setup($rematch = false, $forceRefresh = false)
  {
    $scenario = self::get();
    if (is_null($scenario)) {
      throw new \BgaVisibleSystemException('No scenario loaded');
    }

    // Create Units
    Units::loadScenario($scenario);

  }
}
