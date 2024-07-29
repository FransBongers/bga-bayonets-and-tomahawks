<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Helpers\Utils;

/**
 * Cards
 */
class Spaces extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'spaces';
  protected static $prefix = 'space_';
  protected static $customFields = [
    'battle',
    'defender',
    'control',
    'raided',
    // 'extra_data'
  ];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($row)
  {
    return self::getInstance($row['space_id'], $row);
  }

  public static function getInstance($id, $data = null)
  {
    $className = "\BayonetsAndTomahawks\Spaces\\" . $id;
    return new $className($data);
  }

  public static function getBattleLocations()
  {
    $locations = self::getSelectQuery()
      ->where('battle', '=', 1)
      ->get()
      ->toArray();

    usort($locations, function ($a, $b) {
      return $a->getBattlePriority() - $b->getBattlePriority();
    });
    return $locations;
  }

  public static function getControlledBy($faction)
  {
    $locations = self::getSelectQuery()
    ->where('control', '=', $faction)
    ->get()
    ->toArray();
    return $locations;
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  /* Creation of the cards */
  public static function setupNewGame($players = null, $options = null)
  {
    $spaces = [];
    foreach (SPACES as $spaceId) {
      $space = self::getInstance($spaceId);

      $data = [
        'id' => $spaceId,
        'battle' => 0,
        'control' => $space->getDefaultControl(), // Use homeSpace data?
        'defender' => null,
        'location' => 'default',
        'raided' => null,
        // 'extra_data' => ['properties' => []],
      ];

      $spaces[$spaceId] = $data;
    }
    self::create($spaces, null);
  }

  public static function getUiData()
  {
    return self::getAll()->map(function ($space) {
      return $space->jsonSerialize();
    })->toArray();
  }
}
