<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Models\Marker;

/**
 * extends
 */
class Markers extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'markers';
  protected static $prefix = 'marker_';
  protected static $customFields = [
    // 'extra_data'
  ];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($marker)
  {
    // return [
    //   'id' => $token['marker_id'],
    //   'location' => $token['marker_location'],
    //   'state' => intval($token['marker_state']),
    // ];
    return new Marker($marker);
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
      'location' => Locations::yearTrack($scenario->getStartYear()),
      // 'extra_data' => json_encode(null)
    ];
    $tokens[ROUND_MARKER] = [
      'id' => ROUND_MARKER,
      'location' => ACTION_ROUND_1,
      // 'extra_data' => json_encode(null)
    ];
    $tokens[BRITISH_RAID_MARKER] = [
      'id' => BRITISH_RAID_MARKER,
      'location' => RAID_TRACK_0,
      // 'extra_data' => json_encode(null)
    ];
    $tokens[FRENCH_RAID_MARKER] = [
      'id' => FRENCH_RAID_MARKER,
      'location' => RAID_TRACK_0,
      // 'extra_data' => json_encode(null)
    ];
    $vpMarkerLocation = $scenario->getVictoryMarkerLocation();
    $tokens[VICTORY_MARKER] = [
      'id' => VICTORY_MARKER,
      'location' => $vpMarkerLocation,
      // 'extra_data' => json_encode(null)
    ];

    $players = Players::getAll();
    foreach ($players as $player) {
      if ($vpMarkerLocation === VICTORY_POINTS_FRENCH_1) {
        if ($player->getFaction() === BRITISH) {
          $player->setScore(-1);
        }
        if ($player->getFaction() === FRENCH) {
          $player->setScore(1);
        }
      }
    }


    self::create($tokens, null);
  }
}
