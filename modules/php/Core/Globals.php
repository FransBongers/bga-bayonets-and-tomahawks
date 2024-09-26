<?php

namespace BayonetsAndTomahawks\Core;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Managers\Players;

/*
 * Globals
 */

class Globals extends \BayonetsAndTomahawks\Helpers\DB_Manager
{
  protected static $initialized = false;
  protected static $variables = [
    'engine' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'engineChoices' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE => number of choices a player has made?
    'callbackEngineResolved' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE => function called when engine is resolved?
    'anytimeRecursion' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'customTurnOrders' => 'obj', // DO NOT MODIFY, USED FOR CUSTOM TURN ORDER FEATURE
    'logState' => 'int', // Used to store state id when enabling the log
    'firstPlayer' => 'int',
    // 'activePlayerId' => 'int',
    'scenarioId' => 'str',
    'test' => 'obj',
    'year' => 'int',
    'actionRound' => 'str',
    'firstPlayerId' => 'int',
    'secondPlayerId' => 'int',
    'reactionActionPointId' => 'str',
    // Used in battle
    'activeBattleSpaceId' => 'str',
    'activeBattleAttackerFaction' => 'str',
    'activeBattleDefenderFaction' => 'str',
    'activeBattleHighlandBrigadeHit' => 'bool',
    'activeBattleCoupDeMain' => 'bool',
    'lostAPBritish' => 'obj',
    'lostAPFrench' => 'obj',
    'lostAPIndian' => 'obj',
    'addedAPFrench' => 'obj',
    'controlCherokee' => 'str',
    'controlIroquois' => 'str',
    'placedConstructionMarkers' => 'obj',
    'usedEventBritish' => 'int',
    'usedEventFrench' => 'int', 
    'usedEventIndian' => 'int',
    'noIndianUnitMayBeActivated' => 'bool',
    'winteringRearAdmiralPlayed' => 'bool',
    'highwayUnusableForBritish' => 'str',
  ];

  protected static $table = 'global_variables';
  protected static $primary = 'name';
  protected static function cast($row)
  {
    $val = json_decode(\stripslashes($row['value']), true);
    return (self::$variables[$row['name']] ?? null) == 'int' ? ((int) $val) : $val;
  }

  /*
   * Fetch all existings variables from DB
   */
  protected static $data = [];
  public static function fetch()
  {
    // Turn of LOG to avoid infinite loop (Globals::isLogging() calling itself for fetching)
    $tmp = self::$log;
    self::$log = false;

    foreach (self::DB()
      ->select(['value', 'name'])
      ->get()
      as $name => $variable) {
      if (\array_key_exists($name, self::$variables)) {
        self::$data[$name] = $variable;
      }
    }
    self::$initialized = true;
    self::$log = $tmp;
  }

  /*
   * Create and store a global variable declared in this file but not present in DB yet
   *  (only happens when adding globals while a game is running)
   */
  public static function create($name)
  {
    if (!\array_key_exists($name, self::$variables)) {
      return;
    }

    $default = [
      'int' => 0,
      'obj' => [],
      'bool' => false,
      'str' => '',
    ];
    $val = $default[self::$variables[$name]];
    self::DB()->insert(
      [
        'name' => $name,
        'value' => \json_encode($val),
      ],
      true
    );
    self::$data[$name] = $val;
  }

  /*
   * Magic method that intercept not defined static method and do the appropriate stuff
   */
  public static function __callStatic($method, $args)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = mb_strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, self::$variables)) {
        throw new \InvalidArgumentException("Property {$name} doesn't exist");
      }

      // Create in DB if don't exist yet
      if (!\array_key_exists($name, self::$data)) {
        self::create($name);
      }

      if ($match[1] == 'get') {
        // Basic getters
        return self::$data[$name];
      } elseif ($match[1] == 'is') {
        // Boolean getter
        if (self::$variables[$name] != 'bool') {
          throw new \InvalidArgumentException("Property {$name} is not of type bool");
        }
        return (bool) self::$data[$name];
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        if (!isset($args[0])) {
          throw new \InvalidArgumentException("Setting {$name} require a value");
        }
        $value = $args[0];
        if (self::$variables[$name] == 'int') {
          $value = (int) $value;
        }
        if (self::$variables[$name] == 'bool') {
          $value = (bool) $value;
        }

        self::$data[$name] = $value;
        self::DB()->update(['value' => \addslashes(\json_encode($value))], $name);
        return $value;
      } elseif ($match[1] == 'inc') {
        if (self::$variables[$name] != 'int') {
          throw new \InvalidArgumentException("Trying to increase {$name} which is not an int");
        }

        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        return self::$setter(self::$getter() + (empty($args) ? 1 : $args[0]));
      }
    }
    // return undefined;
  }

  /*
   * Setup new game
   */
  public static function setupNewGame($players, $options)
  {
    // Game options
    self::setAddedAPFrench([]);
    self::setLostAPBritish([]);
    self::setLostAPFrench([]);
    self::setLostAPIndian([]);
    self::setControlCherokee(NEUTRAL);
    self::setControlIroquois(NEUTRAL);
    self::setPlacedConstructionMarkers([]);
    self::setUsedEventBritish(0);
    self::setUsedEventFrench(0);
    self::setUsedEventIndian(0);
    self::setNoIndianUnitMayBeActivated(false);
    self::setWinteringRearAdmiralPlayed(false);
    self::setHighwayUnusableForBritish('');
  }

  public static function getUsedEventCount($faction) {
    if ($faction === BRITISH) {
      return self::getUsedEventBritish();
    } else if ($faction === FRENCH) {
      return self::getUsedEventFrench();
    } else if ($faction === INDIAN) {
      return self::getUsedEventIndian();
    }
    return null;
  }

  public static function setUsedEventCount($faction, $value) {
    if ($faction === BRITISH) {
      return self::setUsedEventBritish($value);
    } else if ($faction === FRENCH) {
      return self::setUsedEventFrench($value);
    } else if ($faction === INDIAN) {
      return self::setUsedEventIndian($value);
    }
    return null;
  }

  public static function incUsedEventCount($faction, $increase) {
    if ($faction === BRITISH) {
      return self::incUsedEventBritish($increase);
    } else if ($faction === FRENCH) {
      return self::incUsedEventFrench($increase);
    } else if ($faction === INDIAN) {
      return self::incUsedEventIndian($increase);
    }
    return null;
  }
}
