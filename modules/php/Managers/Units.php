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
    'class',
    'extra_datas',
  ];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    Notifications::log('cast',$row);
    $row['unit_id'] = $row['id'];
    $row['unit_location'] = $row['location'];

    return self::getInstance($row['class'], $row);
  }

  public function getInstance($class, $row = null)
  {
    $className = '\BayonetsAndTomahawks\Units\\' . UNIT_CLASSES[$class];
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
    $data = [];
    foreach (UNIT_CLASSES as $identifier => $className) {
      $className = '\BayonetsAndTomahawks\Units\\' . $className;
      $unit = new $className(null);
      $data[$unit->getClass()] = $unit->getStaticUiData();
    }
    return $data;
  }

  public static function getUiData()
  {
    return self::getAll()->map(function ($unit) {
      return $unit->jsonSerialize();
    });
  }

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
    Notifications::log('loadUnits',[]);
    // Only needed if we enable rematches?
    self::DB()
      ->delete()
      ->run();
    $locations = $scenario['locations'];
    $units = [];
    Notifications::log('locations',$locations);

    foreach ($locations as &$location) {
      Notifications::log('location',$location);
      
      if (!isset($location['units'])) {
        continue;
      }
      foreach ($location['units'] as &$unit) {
        // $info = self::getInstance($unit);
        $data = [
          'location' => $location['id'],
          'class' => $unit,
          // 'type' => $unit,
        ];
        $data['extra_datas'] = ['properties' => []];
        $units[] = $data;

      }
    }
    Notifications::log('units',$units);
    self::create($units);
  }

  public function remove($unitId)
  {
    $unitId = is_int($unitId) ? $unitId : $unitId->getId();
    self::DB()->delete($unitId);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

}
