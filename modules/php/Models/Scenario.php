<?php

namespace BayonetsAndTomahawks\Models;

class Scenario implements \JsonSerializable
{
  protected $id;
  protected $name;
  protected $number;
  protected $startYear;
  protected $duration;
  protected $locations;
  protected $reinforcements;
  protected $pools;
  protected $victoryMarkerLocation;

  public function __construct()
  {

  }

  protected $attributes = [
    'id' => ['id', 'str'],
    'name' => ['name', 'str'],
    'number' => ['number', 'int'],
    'startYear' => ['startYear', 'int'],
    'duration' => ['duration', 'int'],
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

  public function getVictoryMarkerLocation()
  {
    return $this->victoryMarkerLocation;
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
