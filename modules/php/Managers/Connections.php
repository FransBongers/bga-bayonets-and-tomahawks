<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Helpers\Utils;

use const BayonetsAndTomahawks\OPTION_STARTING_MAP_AGE_OF_REFORMATION_PROMO_VARIANT;

/**
 * Cards
 */
class Connections extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'connections';
  protected static $prefix = 'connection_';
  protected static $customFields = ['british_limit', 'french_limit', 'road'];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($connections)
  {
    return self::getConnectionInstance($connections['connection_id'], $connections);
  }


  public static function getConnectionInstance($id, $data = null)
  {
    // $prefix = self::getClassPrefix($id);

    $className = "\BayonetsAndTomahawks\Connections\\$id";
    return new $className($data);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////


  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  private static function setupLoadConnections()
  {
    // Load list of cards
    include dirname(__FILE__) . '/../Connections/list.inc.php';

    $connections = [];

    // return;
    foreach ($connectionIds as $cId) {
      // $card = self::getCardInstance($cId);

      $extraData = [
        'britishLimitUsed' => 0,
        'frenchLimitUsed' => 0,
      ];
      // // $location = 'deck';



      $connections[$cId] = [
        'id' => $cId,
        'location' => null,
        'state' => 0,
        'britishLimit' => 0,
        'frenchLimit' => 0,
        'road' => 0,
        // 'extra_data' => json_encode($extraData)
      ];
    }

    // Add data to db
    self::create($connections, 'map');
  }

  /* Creation of the cards */
  public static function setupNewGame($players = null, $options = null)
  {
    self::setupLoadConnections();
  }

  /**
   * Load a scenario
   */
  public static function loadScenario($scenario)
  {
    $connections = $scenario->getConnections();
    foreach ($connections as $connectionId => &$connectionData) {
      $connection = self::get($connectionId);

      if (!isset($connectionData['markers'])) {
        continue;
      }

      foreach ($connectionData['markers'] as $markerType) {
        if ($markerType === ROAD_MARKER) {
          $connection->setRoad(HAS_ROAD);
        }
      }
    }
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  public static function getUiData()
  {
    // return self::getMany([YORK_ZAWAKWTEGOK, ALBANY_KINGSTON])->map(function ($connection) {
    //   return $connection->jsonSerialize();
    // })->toArray();
    return self::getAll()->map(function ($connection) {
      return $connection->jsonSerialize();
    })->toArray();
  }

  /**
   * getStaticUiData : return all units static datas
   */
  public static function getStaticUiData()
  {
    $connections = self::getAll()->toArray();

    $data = [];
    foreach ($connections as $index => $connection) {
      $data[$connection->getId()] = $connection->getStaticData();
    }
    return $data;
  }

  public static function resetConnectionLimits()
  {
    self::DB()->update(['british_limit' => 0])->run();
    self::DB()->update(['french_limit' => 0])->run();
  }
}
