<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;

class AbstractUnit extends \BayonetsAndTomahawks\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'units';
  protected $primary = 'unit_id';
  protected $attributes = [
    'id' => ['unit_id', 'int'],
    'location' => ['unit_location', 'str'],
    'counterId' => ['counter_id', 'str'],
    'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $counterId;
  protected $faction = null;
  protected $location = null;
  protected $datas = null;
  protected $indian = false;
  protected $mpLimit = 0;
  protected $connectionTypeAllowed = [];
  
  /*
   * STATIC DATA
   */
  protected $staticAttributes = ['counterId', 'counterText', 'faction', 'type', 'mpLimit', 'connectionTypeAllowed'];
  protected $type = null;
  protected $counterText = null;

  /*
   * UNIT PROPERTIES
   * - stored in extra datas
   */
  protected $properties = [
  ];


  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
    }
  }

  public function applyPropertiesModifiers()
  {
    $prop = $this->getExtraDatas('properties') ?? [];
    foreach ($prop as $name => $value) {
      $this->$name = $value;
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'counterId' => $this->counterId,
      'location' => $this->location,
    ];
  }

  public function getStaticUiData()
  {
    $t = array_merge($this->staticAttributes, $this->properties);
    $data = [];
    foreach ($t as $prop) {
      if (isset($this->$prop)) {
        $data[$prop] = $this->$prop;
      }
    }

    return $data;
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  public function getProperty($prop)
  {
    if (!in_array($prop, $this->properties)) {
      throw new \BgaVisibleSystemException('Trying to access a non existing unit property : ' . $prop);
    }

    return $this->$prop ?? null;
  }

  public function getFaction()
  {
    return $this->faction;
  }

  public function isIndian()
  {
    return $this->indian;
  }

  public function getType()
  {
    return $this->type;
  }

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    return $this->getProperty($method);
  }
}
