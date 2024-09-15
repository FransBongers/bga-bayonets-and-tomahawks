<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Scenarios\AmherstsJuggernaut1758_1759;

class Scenarios
{
  // Mapping of opId and corresponding class
  static $scenarios = [
    VaudreuilsPetiteGuerre1755 => 'VaudreuilsPetiteGuerre1755',
    LoudounsGamble1757 => 'LoudounsGamble1757',
    AmherstsJuggernaut1758_1759 => 'AmherstsJuggernaut1758_1759'
  ];

  public static function get($scenarioId = null)
  {
    $scenarioId = $scenarioId === null ? Globals::getScenarioId() : $scenarioId;
    if (!\array_key_exists($scenarioId, self::$scenarios)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get a scenario not defined in Scenarios.php : ' . $scenarioId);
    }
    $name = '\BayonetsAndTomahawks\Scenarios\\' . self::$scenarios[$scenarioId];
    return new $name();
  }

  static function setup($scenarioOption)
  {
    $scenarioIdMap = [
      1 => VaudreuilsPetiteGuerre1755,
      2 => LoudounsGamble1757,
      3 => AmherstsJuggernaut1758_1759,
    ];

    $scenarioId = $scenarioIdMap[intval($scenarioOption)];
    $scenario = self::get($scenarioId);
    Globals::setYear($scenario->getStartYear());
    Globals::setActionRound(ACTION_ROUND_1);
    Globals::setScenarioId($scenarioId);

    // Create Units
    Units::loadScenario($scenario);
    // Add markers to connections
    Connections::loadScenario($scenario);
  }

  // public static function getAll()
  // {
  //   return array_map(function ($opId) {
  //     return self::get($opId);
  //   }, array_keys(self::$ops));
  // }


}
