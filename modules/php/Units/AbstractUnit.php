<?php
namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;

class AbstractUnit extends \BayonetsAndTomahawks\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'units';
  protected $primary = 'unit_id';
  protected $attributes = [
    'id' => ['unit_id', 'int'],
    'location' => ['unit_location', 'str'],
    'extraDatas' => ['extra_datas', 'obj'],
  ];

  protected $id = null;
  protected $faction = null;
  protected $location = null;
  protected $datas = null;

  /*
   * STATIC INFORMATIONS
   */
  protected $staticAttributes = ['class', 'type', 'name', 'faction'];
  protected $class = null;
  protected $type = null;
  protected $name = null;

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
      'class' => $this->class,
      'location' => $this->location,
    ];
  }

  public function getStaticUiData()
  {
    $t = array_merge($this->staticAttributes, $this->properties);
    $datas = [];
    foreach ($t as $prop) {
      if (isset($this->$prop)) {
        $datas[$prop] = $this->$prop;
      }
    }

    return $datas;
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

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    return $this->getProperty($method);
  }
}
