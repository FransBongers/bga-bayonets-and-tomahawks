<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Markers;
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
  protected $controlStartOfTurn = null;
  protected $defender = null;
  protected $fortConstruction;
  protected $raided = null;
  protected $unitsStartOfTurn = null;
  
  protected $attributes = [
    'id' => ['space_id', 'int'],
    'battle' => ['battle', 'int'],
    'control' => ['control', 'str'],
    'controlStartOfTurn' => ['control_start_of_turn', 'str'],
    'defender' => ['defender', 'str'],
    'fortConstruction' => ['fort_construction', 'int'],
    'location' => ['space_location', 'str'],
    'state' => ['space_state', 'int'],
    'raided' => ['raided', 'str'],
    'unitsStartOfTurn' => ['units_start_of_turn', 'str'], // Faction that had units on space at start of turn
    // 'extraData' => ['extra_data', 'obj'],
  ];


  protected $staticAttributes = [
    'adjacentSeaZones',
    'adjacentSpaces',
    'battlePriority',
    'britishBase',
    'coastal',
    'colony',
    'homeSpace',
    'indianVillage',
    'isSpaceOnMap',
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

  protected $adjacentSeaZones = [];
  protected $battlePriority;
  protected $britishBase = false;
  protected $coastal = false;
  protected $colony = null;
  protected $defaultControl;
  protected $faction = null;
  protected $homeSpace = null;
  protected $indianVillage = null;
  protected $isSpaceOnMap = true;
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
      'colony' => $this->colony,
      'control' => $this->control,
      'controlStartOfTurn' => $this->controlStartOfTurn,
      'defaultControl' => $this->getDefaultControl(),
      'defender' => $this->defender,
      'fortConstruction' => $this->fortConstruction === 1,
      'homeSpace' => $this->homeSpace,
      'name' => $this->name,
      'raided' => $this->raided,
      'victorySpace' => $this->victorySpace,
      'top' => $this->top,
      'left' => $this->left,
      // 'unitsStartOfTurn' => $this->unitsStartOfTurn,
    ];
  }


  public function getAdjacentConnections()
  {
    $result = [];
    // $spaces = Spaces::getMany($this->getAdjacentSpacesIds());

    foreach ($this->adjacentSpaces as $spaceId => $connectionId) {
      // TODO: query all connections in one go?
      $result[$spaceId] = Connections::get($connectionId);
      // TODO: check where this is used and refactor to use space data
      // $result[$spaceId]['space'] = $spaces[$spaceId];
    };
    return $result;
  }

  // TODO: refactor to use this and above unit everywhere
  public function getAdjacentConnectionsAndSpaces()
  {
    $result = [];
    $spaces = Spaces::getMany($this->getAdjacentSpacesIds());

    foreach ($this->adjacentSpaces as $spaceId => $connectionId) {
      // TODO: query all connections in one go?
      $result[] = [
        'space' => $spaces[$spaceId],
        'connection' => Connections::get($connectionId)
      ];
      // TODO: check where this is used and refactor to use space data
      // $result[$spaceId]['space'] = $spaces[$spaceId];
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
    if ($this->id === LOUISBOURG) {
      $bastion1 = Units::getTopOf(LOUISBOURG_BASTION_1);
      $bastion2 = Units::getTopOf(LOUISBOURG_BASTION_2);
      foreach([$bastion1, $bastion2] as $unit) {
        if ($unit !== null) {
          $units[] = $unit;
        }
      }
    }
    if ($this->id === QUEBEC) {
      $bastion1 = Units::getTopOf(QUEBEC_BASTION_1);
      $bastion2 = Units::getTopOf(QUEBEC_BASTION_2);
      foreach([$bastion1, $bastion2] as $unit) {
        if ($unit !== null) {
          $units[] = $unit;
        }
      }
    }

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

  public function isCoastal()
  {
    return $this->coastal;
  }

  public function isHomeSpace($faction)
  {
    return $this->homeSpace === $faction;
  }

  public function isOutpost()
  {
    // Can change this to based on value === 1?
    return $this->outpost;
  }

  public function isWildernessSpace()
  {
    return $this->value === 0 && $this->indianVillage === null;
  }

  public function isVictorySpace()
  {
    return $this->victorySpace;
  }

  public function isControlledBy($faction)
  {
    return $this->control === $faction;
  }

  public function isSettledSpace($faction = null)
  {
    if ($faction === null) {
      return $this->value > 1;
    }
    return $this->homeSpace === $faction && $this->value > 1;
  }

  public function isSpaceOnMap() {
    return $this->isSpaceOnMap;
  }

  public function isFriendlyColonyHomeSpace($faction)
  {
    return $this->getHomeSpace() === $faction && $this->getColony() !== null && $this->getControl() === $faction;
  }

  public function hasStackMarker($type, $faction)
  {
    $markers = Markers::getOfTypeInLocation($type, Locations::stackMarker($this->getId(), $faction));
    return count($markers) > 0;
  }
}
