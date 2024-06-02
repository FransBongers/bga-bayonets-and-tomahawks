<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

/**
 * Space
 */
class Space extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $id;
  protected $table = 'spaces';
  protected $primary = 'space_id';
  protected $battle = 0;
  protected $control = null;
  protected $raided = null;

  protected $attributes = [
    'id' => ['space_id', 'int'],
    'battle' => ['battle', 'int'],
    'control' => ['control', 'str'],
    'defender' => ['defender', 'str'],
    'location' => ['space_location', 'str'],
    'state' => ['space_state', 'int'],
    'raided' => ['raided', 'str'],
    // 'extraData' => ['extra_data', 'obj'],
  ];


  protected $staticAttributes = [
    'battlePriority',
    'britishBase',
    'homeSpace',
    'militia',
    'name',
    'outpost',
    'settledSpace',
    'value',
    'victorySpace',
    'defaultControl',
    'top',
    'left'
  ];

  protected $battlePriority;
  protected $britishBase = false;
  protected $defaultControl;
  protected $faction = null;
  protected $homeSpace = null;
  protected $outpost = false;
  protected $settledSpace = false;
  protected $militia = 0;
  protected $name = null;
  protected $value = 0;
  protected $victorySpace = false;
  protected $top = 0;
  protected $left = 0;
  protected $adjacentSpaces = [];

  // public function __construct($row)
  // {
  //   if ($row != null) {
  //     parent::__construct($row);
  //   }
  // }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'battle' => $this->battle === 1,
      'control' => $this->control,
      'defaultControl' => $this->defaultControl,
      'homeSpace' => $this->homeSpace,
      'name' => $this->name,
      'raided' => $this->raided,
      'victorySpace' => $this->victorySpace,
      'top' => $this->top,
      'left' => $this->left,
    ];
  }

  public function getAdjacentSpaces()
  {
    $result = [];
    foreach ($this->adjacentSpaces as $spaceId => $connectionId) {
      $result[$spaceId] = Connections::get($connectionId);
    };
    return $result;
  }

  public function getAdjacentSpacesIds()
  {
    return array_keys($this->adjacentSpaces);
  }

  public function getUnits($faction = null)
  {
    $units = Units::getInLocation($this->id)->toArray();
    if ($faction === null) {
      return $units;
    }
    return Utils::filter($units, function ($unit) use ($faction) {
      return $unit->getFaction() === $faction;
    });
  }

  public function hasBastion()
  {
    return false;
  }
}
