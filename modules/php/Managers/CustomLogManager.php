<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\PlayersExtra;


/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class CustomLogManager extends \BayonetsAndTomahawks\Helpers\DB_Manager
{
  protected static $table = 'custom_log';
  protected static $primary = 'id';
  protected static function cast($row)
  {
    return new \BayonetsAndTomahawks\Models\CustomLogRecord($row);
  }

  public static function setupNewGame($players, $options)
  {
  }

  public static function addRecord($type, $data)
  {
    $round = explode('_',Globals::getActionRound())[3];
    $year = Globals::getYear();
    self::DB()->insert([
      'type' => $type,
      'round' => $round,
      'year' => $year,
      'data' => json_encode($data),
    ]);
  }

  public static function getAll()
  {
    $logs = self::DB()->get(false);
    return $logs;
  }



}
