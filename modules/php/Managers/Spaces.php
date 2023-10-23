<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;

// require_once 'modules/php/Spaces/DefaultSpaces.php';
// 

/**
 * Units
 */
class Spaces extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'spaces';
  protected static $prefix = 'space_';
  protected static $customFields = ['control', 'extra_datas'];
  protected static $autoIncrement = false;

  protected static function cast($space)
  {
    Notifications::log('cast in manager',$space);
    return self::getSpaceInstance($space['location'], $space);
  }

  public static function getSpaceInstance($id, $data = null)
  {
    $className = "\BayonetsAndTomahawks\Spaces\\".$id;
    return new $className($data);
  }

  public static function getUiData()
  {
    return self::getAll()->map(function ($space) {
      return $space->jsonSerialize();
    });
  }

  // public static function setupNewGame($players, $options)
  public static function setupNewGameDefaults()
  {
    $spaces = [];
    foreach (SPACES as $spaceId) {
      $space = self::getSpaceInstance($spaceId);
      $data = [
        'id' => $space->getId(),
        'location' => $space->getLocation(),
        'control' => $space->getControl(),
      ];
      $spaces[] = $data;
    }

    self::create($spaces);
  }


}
