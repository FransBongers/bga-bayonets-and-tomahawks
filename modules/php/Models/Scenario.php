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
  protected $pools;

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

  public function getStartYear()
  {
    return $this->startYear;
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
