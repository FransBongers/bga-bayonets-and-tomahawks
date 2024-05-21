<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Helpers\Utils;

/**
 * Units
 */
class Units extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'units';
  protected static $prefix = 'unit_';
  protected static $customFields = [
    'counter_id',
    'spent',
    'extra_data',
  ];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;
  
  protected static function cast($row)
  {
    // Notifications::log('cast',$row);
    return self::getInstance($row['counter_id'], $row);
  }

  public static function getInstance($counterId, $row = null)
  {
    $className = '\BayonetsAndTomahawks\Units\\' . $counterId;
    return new $className($row);
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  /**
   * getStaticUiData : return all units static datas
   */
  public static function getStaticUiData()
  {
    $units = self::getAll()->toArray();

    $data = [];
    foreach ($units as $index => $unit) {
      $counterId = $unit->getCounterId();
      $className = '\BayonetsAndTomahawks\Units\\' . $counterId;
      $unit = new $className(null);
      $data[$unit->getCounterId()] = $unit->getStaticUiData();
    }
    return $data;
  }

  public static function getUiData()
  {
    return self::getAll()->map(function ($unit) {
      return $unit->jsonSerialize();
    })->toArray();
  }

  // public static function get($id, $raiseExceptionIfNotEnough = true)
  // {
  //   $result = self::getMany($id, $raiseExceptionIfNotEnough);
  //   return $result->count() == 1 ? $result->first() : $result;
  // }

  // public static function getMany($ids, $raiseExceptionIfNotEnough = true)
  // {
  //   if (!is_array($ids)) {
  //     $ids = [$ids];
  //   }

  //   $ids = array_map(function ($id) {
  //     if (Utils::startsWith($id, 'unit')) {
  //       return intval(explode('_', $id)[1]);
  //     } else {
  //       return $id;
  //     }
  //   }, $ids);

  //   return parent::getMany($ids, $raiseExceptionIfNotEnough);
  // }

  // ..######..########.########.########.########.########...######.
  // .##....##.##..........##.......##....##.......##.....##.##....##
  // .##.......##..........##.......##....##.......##.....##.##......
  // ..######..######......##.......##....######...########...######.
  // .......##.##..........##.......##....##.......##...##.........##
  // .##....##.##..........##.......##....##.......##....##..##....##
  // ..######..########....##.......##....########.##.....##..######.

  /**
   * Load a scenario
   */
  public static function loadScenario($scenario)
  {
    Notifications::log('loadUnits', []);
    // Only needed if we enable rematches?
    // self::DB()
    //   ->delete()
    //   ->run();

    $unitIdIndex = 1;

    $units = [];

    // Units in locations
    $locations = $scenario->getLocations();
    foreach ($locations as &$location) {

      if (!isset($location['units'])) {
        continue;
      }
      foreach ($location['units'] as &$unit) {
        // $info = self::getInstance($unit);
        $id = 'unit_' . $unitIdIndex;
        $data = [
          'id' => 'unit_' . $unitIdIndex,
          'location' => $location['id'],
          'counter_id' => $unit,
          // 'type' => $unit,
        ];
        $data['extra_data'] = ['properties' => []];
        $units[$id] = $data;
        $unitIdIndex += 1;
      }
    }

    // Units in pools
    $pools = $scenario->getPools();
    foreach ($pools as $poolId => $pool) {
      Notifications::log('pool', $pool);

      if (!isset($pool['units'])) {
        continue;
      }
      foreach ($pool['units'] as &$unit) {
        // $info = self::getInstance($unit);
        $id = 'unit_' . $unitIdIndex;
        $data = [
          'id' => $id,
          'location' => $poolId,
          'counter_id' => $unit,
          // 'type' => $unit,
        ];
        $data['extra_data'] = ['properties' => []];
        $units[$id] = $data;
        $unitIdIndex += 1;
      }
    }
    Notifications::log('units', $units);
    self::create($units, null);
  }

  // public function remove($unitId)
  // {
  //   $unitId = is_int($unitId) ? $unitId : $unitId->getId();
  //   self::DB()->delete($unitId);
  // }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

}
