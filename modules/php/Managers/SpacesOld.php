<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;

// require_once 'modules/php/Spaces/DefaultSpaces.php';
// 


class SpacesOld extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'spaces';
  protected static $prefix = 'space_';
  protected static $primary = 'space_id';
  protected static $customFields = [
    'control',
    'raided',
    // 'extra_data',
  ];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  
  protected static function cast($row)
  {
    // $instance = self::get($row['space_id'],$row);
    // return $instance;
    return self::getInstance($row['space_id'], $row);
  }

  public static function getInstance($spaceId, $row = null)
  {
    $className = '\BayonetsAndTomahawks\Spaces\\' . $spaceId;
    return new $className($row);
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
      $space = self::getInstance($spaceId);
      $data = [
        'id' => $spaceId,
        'control' => $space->getDefaultControl(),
        'location' => 'default',
        'raided' => null,
        // 'extra_data' => ['properties' => []],
      ];
      $spaces[$spaceId] = $data;
    }
    Notifications::log('spaces', $spaces);

    self::create($spaces);

    // Create players
    // $gameInfos = Game::get()->getGameinfos();
    // $colors = $gameInfos['player_colors'];
    // $query = self::DB()->multipleInsert([
    //   'space_id',
    //   'control',
    //   'raided',
    //   // 'extra_data',
    // ]);

    // $values = [];
    // foreach ($players as $pId => $player) {
    //   $color = array_shift($colors);
    //   $values[] = [$pId, $color, $player['player_canal'], $player['player_name'], $player['player_avatar'],0];
    // }
  //   Notifications::log('spaces',$spaces);
  //   $query->values($spaces);
  }

  // protected static function cast($space)
  // {
  //   Notifications::log('cast in manager', $space);
  //   return self::getSpaceInstance($space['location'], $space);
  // }

  // public static function get($id, $data = null)
  // {
  //   $className = "\BayonetsAndTomahawks\Spaces\\" . $id;
  //   Notifications::log('get data', $data);
  //   return new $className($data);
  // }

  // public static function getAll()
  // {
  //   $spaces = self::DB()->get(false);
  //   return $spaces;
  // }

  public static function getUiData()
  {
    return self::getAll()->map(function ($space) {
      return $space->jsonSerialize();
    })->toArray();
  }

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
