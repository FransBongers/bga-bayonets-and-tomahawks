<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\StackActions;

class Connection extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $id;
  protected $table = 'connections';
  protected $primary = 'connection_id';
  protected $location;
  protected $state;
  protected $limit;
  protected $limitUsed;
  protected $type;
  protected $indianPath = false;
  protected $coastal = false;
  // TODO: add road construction

  protected $attributes = [
    'id' => ['connection_id', 'str'],
    'location' => 'connection_location',
    'state' => ['connection_state', 'int'],
    'extraData' => ['extra_data', 'obj'],
  ];


  protected $staticAttributes = [
    'id',
    'limit',
    'type',
  ];

  public function getId()
  {
    return $this->id;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getLimitUsed($faction)
  {
    $key = $faction . 'LimitUsed';
    return $this->getExtraData($key);
  }

  public function setLimitUsed($faction, $value)
  {
    $key = $faction . 'LimitUsed';
    return $this->setExtraData($key, $value);
  }

  public function incLimitUsed($faction, $increase)
  {
    $current = $this->getLimitUsed($faction);
    $this->setLimitUsed($faction, $current + $increase);
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
