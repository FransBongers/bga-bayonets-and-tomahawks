<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;

class Scenario implements \JsonSerializable
{
  protected $id;
  protected $name;
  protected $number;
  protected $startYear;
  protected $duration;
  protected $indianSetup;
  protected $locations;
  protected $reinforcements;
  protected $pools;
  protected $victoryMarkerLocation;
  protected $victoryThreshold = [];

  public function __construct()
  {
  }

  protected $attributes = [
    'id' => ['id', 'str'],
    'name' => ['name', 'str'],
    'number' => ['number', 'int'],
    'startYear' => ['startYear', 'int'],
    'duration' => ['duration', 'int'],
    'indianSetup' => ['indianSetup', 'obj'],
    'locations' => ['locations', 'obj'],
    'pools' => ['pools', 'obj'],
    'reinforcements' => ['reinforcements', 'obj'],
  ];


  public function getId()
  {
    return $this->id;
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function getIndianSetup()
  {
    return $this->indianSetup;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getLocations()
  {
    return $this->locations;
  }

  public function getPools()
  {
    return $this->pools;
  }

  public function getReinforcements()
  {
    return $this->reinforcements;
  }

  public function getStartYear()
  {
    return $this->startYear;
  }

  public function getYearEndBonus($faction, $year)
  {
    return 0;
  }

  public function getVictoryMarkerLocation()
  {
    return $this->victoryMarkerLocation;
  }

  public function hasAchievedVictoryThreshold($faction, $year)
  {

    $vpMarker = Markers::get(VICTORY_MARKER);
    // 'victory_points_' . $faction . '_' . $score;
    $splitLocation = explode('_', $vpMarker->getLocation());
    // TODO: 'negative vp threshold'
    if ($faction === $splitLocation[2] && intval($splitLocation[3]) >= $this->victoryThreshold[$faction][$year]) {
      return true;
    }
    return false;
  }

  /**
   * Return an array of attributes
   */
  public function jsonSerialize()
  {
    $data = [];
    foreach ($this->attributes as $attribute => $field) {
      $data[$attribute] = $this->$attribute;
    }

    return $data;
  }
}
