<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;

// require_once 'modules/php/Spaces/DefaultSpaces.php';
// 

/**
 * Units
 */
class Spaces extends \BayonetsAndTomahawks\Helpers\DB_Manager
{
  protected static $table = 'spaces';
  protected static $primary = 'space_id';

  protected static function cast($row)
  {
    $instance = self::getSpaceInstance($row['space_id'],$row);
    return $instance;
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  public static function setupNewGame($players = null, $options = null)
  {
    $spaces = [];
    foreach (SPACES as $spaceId) {
      $space = self::getSpaceInstance($spaceId);
      $data = [
        'space_id' => $space->getId(),
        'control' => $space->getDefaultControl(),
        // 'extra_data' => ['properties' => []],
      ];
      $spaces[] = $data;
    }

    // self::create($spaces);

    // Create players
    // $gameInfos = Game::get()->getGameinfos();
    // $colors = $gameInfos['player_colors'];
    $query = self::DB()->multipleInsert([
      'space_id',
      'control',
      // 'extra_data',
    ]);

    // $values = [];
    // foreach ($players as $pId => $player) {
    //   $color = array_shift($colors);
    //   $values[] = [$pId, $color, $player['player_canal'], $player['player_name'], $player['player_avatar'],0];
    // }
    Notifications::log('spaces',$spaces);
    $query->values($spaces);
  }

  // protected static function cast($space)
  // {
  //   Notifications::log('cast in manager', $space);
  //   return self::getSpaceInstance($space['location'], $space);
  // }

  public static function getSpaceInstance($id, $data = null)
  {
    $className = "\BayonetsAndTomahawks\Spaces\\" . $id;
    return new $className($data);
  }

  public static function getAll()
  {
    $spaces = self::DB()->get(false);
    return $spaces;
  }

  // public static function getUiData()
  // {
  //   return self::getAll()->map(function ($space) {
  //     return $space->jsonSerialize();
  //   });
  // }

  // public static function setupNewGame($players, $options)
  // public static function setupNewGameDefaults()
  // {
  //   $spaces = [];
  //   foreach (SPACES as $spaceId) {
  //     $space = self::getSpaceInstance($spaceId);
  //     $data = [
  //       'id' => $space->getId(),
  //       'location' => $space->getLocation(),
  //       'control' => $space->getControl(),
  //     ];
  //     $spaces[] = $data;
  //   }

  //   self::create($spaces);
  // }
}
