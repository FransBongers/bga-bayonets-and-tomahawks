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
  protected $britishLimit;
  protected $frenchLimit;
  protected $type;
  protected $indianPath = false;
  protected $coastal = false;
  // TODO: add road construction

  protected $attributes = [
    'id' => ['connection_id', 'str'],
    'location' => 'connection_location',
    'state' => ['connection_state', 'int'],
    'britishLimit' => ['british_limit', 'int'],
    'frenchLimit' => ['french_limit', 'int'],
    // 'extraData' => ['extra_data', 'obj'],
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
    if ($faction === BRITISH) {
      return $this->britishLimit;
    } else {
      return $this->frenchLimit;
    }
    // $key = $faction . 'LimitUsed';
    // return $this->getExtraData($key);
  }

  public function setLimitUsed($faction, $value)
  {
    if ($faction === BRITISH) {
      return $this->setBritishLimit($value);
    } else {
      return $this->setFrenchLimit($value);
    }
    // $key = $faction . 'LimitUsed';
    // return $this->setExtraData($key, $value);
  }

  public function incLimitUsed($faction, $increase)
  {
    if ($faction === BRITISH) {
      return $this->incBritishLimit($increase);
    } else {
      return $this->incFrenchLimit($increase);
    }
    // $current = $this->getLimitUsed($faction);
    // $this->setLimitUsed($faction, $current + $increase);
  }

  public function isCoastalConnection()
  {
    return $this->coastal;
  }

  public function canBeUsedByUnits($units, $ignoreLimit = false)
  {
    // TODO: connection limits
    $hasFleet = Utils::array_some($units, function ($unit) {
      return $unit->isFleet();
    });
    if ($hasFleet && !$this->isCoastalConnection()) {
      return false;
    }

    $requiresRoadOrHighway = Utils::array_some($units, function ($unit) {
      // TODO: check commander and light units only?
      return $unit->isBrigade() || $unit->isCommander() || $unit->isArtillery();
    });

    if ($requiresRoadOrHighway && !in_array($this->getType(), [HIGHWAY, ROAD])) {
      return false;
    }

    return true;
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
