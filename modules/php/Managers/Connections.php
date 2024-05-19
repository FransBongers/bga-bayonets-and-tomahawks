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
  protected static $customFields = ['british_limit', 'french_limit'];
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
    // Notifications::log('scenario', $scenario);
    // // return;
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
}
