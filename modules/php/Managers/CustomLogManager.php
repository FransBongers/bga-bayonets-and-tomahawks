<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\PlayersExtra;


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
    $dbData = [
      'type' => $type,
      'round' => $round,
      'year' => $year,
      'data' => json_encode($data),
    ];
    self::DB()->insert($dbData);
    return [
      'type' => $type,
      'round' => $round,
      'year' => $year,
      'data' => $data,
    ];
  }

  public static function get($id)
  {
    $logs = self::DB()->where('id', $id)->get(true);
    return $logs;
  }


  public static function getAll()
  {
    $logs = self::DB()->get(false)->toArray();
    return $logs;
  }



}
