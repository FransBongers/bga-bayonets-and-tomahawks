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
    'control_start_of_turn',
    'fort_construction',
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

  public static function setStartOfTurnControl()
  {
    self::DB()->update(['control_start_of_turn' => BRITISH])->where('control', '=', BRITISH)->run();
    self::DB()->update(['control_start_of_turn' => FRENCH])->where('control', '=', FRENCH)->run();
    self::DB()->update(['control_start_of_turn' => NEUTRAL])->where('control', '=', NEUTRAL)->run();
  }

  public static function removeAllRaidedMarkers()
  {
    self::DB()->update(['raided' => null])->whereNotNull('raided')->run();
  }

  public static function getAllRaidedSpaces()
  {
    $spaces = self::getSelectQuery()
      ->whereNotNull('raided')
      ->get()
      ->toArray();
    return $spaces;
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
        'control_start_of_turn' => $space->getDefaultControl(), // Use homeSpace data?
        'defender' => null,
        'fort_construction' => 0,
        'location' => 'default',
        'raided' => null,
        // 'extra_data' => ['properties' => []],
      ];

      $spaces[$spaceId] = $data;
    }

    foreach (BOXES as $spaceId) {
      $space = self::getInstance($spaceId);

      $data = [
        'id' => $spaceId,
        'battle' => 0,
        'control' => NEUTRAL,
        'control_start_of_turn' => NEUTRAL,
        'defender' => null,
        'fort_construction' => 0,
        'location' => 'default',
        'raided' => null,
      ];

      $spaces[$spaceId] = $data;
    }

    self::create($spaces, null);
  }

  public static function setupBoxes()
  {
    $spaces = [];

    foreach (BOXES as $spaceId) {
      $data = [
        'id' => $spaceId,
        'battle' => 0,
        'control' => NEUTRAL,
        'control_start_of_turn' => NEUTRAL,
        'defender' => null,
        'fort_construction' => 0,
        'location' => 'default',
        'raided' => null,
      ];

      $spaces[$spaceId] = $data;
    }

    self::create($spaces, null);
  }

  public static function getUiData()
  {
    $spaces = Utils::filter(self::getAll()->toArray(), function ($space) {
      return $space->isSpaceOnMap();
    });

    return array_map(function ($space) {
      return $space->jsonSerialize();
    }, $spaces);
  }

  /**
   * getStaticUiData : return all units static datas
   */
  public static function getStaticUiData()
  {
    $spaces = self::getAll()->toArray();

    $data = [];
    foreach ($spaces as $index => $space) {
      $data[$space->getId()] = $space->getStaticData();
    }
    return $data;
  }
}
