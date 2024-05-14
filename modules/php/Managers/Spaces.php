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
        'control' => $space->getDefaultControl(),
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
