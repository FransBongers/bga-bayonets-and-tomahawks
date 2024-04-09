<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Helpers\Utils;

/**
 * extends
 */
class Tokens extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'tokens';
  protected static $prefix = 'token_';
  protected static $customFields = ['extra_data'];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($token)
  {
    return [
      'id' => $token['token_id'],
      'location' => $token['token_location'],
      'state' => intval($token['token_state']),
    ];
  }


  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  // public static function getOfTypeInLocation($type, $location)
  // {
  //   return self::getSelectQuery()
  //     ->where(static::$prefix . 'id', 'LIKE', $type . '%')
  //     ->where(static::$prefix . 'location', 'LIKE', $location . '%')
  //     ->get()
  //     ->toArray();
  // }

  // public static function getUiData()
  // {
  //   return self::getPool()
  //     ->merge(self::getInLocationOrdered('inPlay'))
  //     ->merge(self::getInLocation('base_%'))
  //     ->merge(self::getInLocation('projects_%'))
  //     ->ui();
  // }

  // public static function getStaticData()
  // {
  //   $cards = Cards::getAll();
  //   $staticData = [];
  //   foreach($cards as $cardId => $card) {
  //     if ($card->getType() !== TABLEAU_CARD) {
  //       continue;
  //     }
  //     $staticData[explode('_',$card->getId())[0]] = $card->getStaticData();
  //   }
  //   return $staticData;
  // }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  /* Creation of the tokens */
  public static function setupNewGame($players = null, $options = null)
  {
    $tokens = [];
    $scenario = Scenarios::get();

    $tokens[YEAR_MARKER] = [
      'id' => YEAR_MARKER,
      'location' => $scenario->getStartYear(),
      'extra_data' => json_encode(null)
    ];
    $tokens[ROUND_MARKER] = [
      'id' => ROUND_MARKER,
      'location' => ACTION_ROUND_1,
      'extra_data' => json_encode(null)
    ];

    self::create($tokens, null);
  }
}
