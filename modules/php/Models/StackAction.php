<?php

namespace BayonetsAndTomahawks\Models;

class StackAction implements \JsonSerializable
{
  protected $id;
  protected $name;


  public function __construct()
  {

  }

  protected $attributes = [
    'id' => ['id', 'str'],
    'name' => ['name', 'str'],
  ];


  public function getId()
  {
    return $this->id;
  }

  public function canBePerformedBy($units) {
    return false;
  }

  public function getFlow($playerId, $originId, $faction)
  {
    return [];
  }

  public function getName()
  {
    return $this->name;
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
