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
  protected $road;
  protected $type;
  protected $indianNationPath = null;
  protected $coastal = false;
  protected $top = 0;
  protected $left = 0;
  // TODO: add road construction

  protected $attributes = [
    'id' => ['connection_id', 'str'],
    'location' => 'connection_location',
    'state' => ['connection_state', 'int'],
    'britishLimit' => ['british_limit', 'int'],
    'frenchLimit' => ['french_limit', 'int'],
    'road' => ['road', 'int'],
    // 'extraData' => ['extra_data', 'obj'],
  ];

  protected $staticAttributes = [
    'id',
    'top',
    'left',
    'coastal',
    'indianNationPath'
  ];

  public function getId()
  {
    return $this->id;
  }

  public function getType()
  {
    return $this->road === HAS_ROAD ? ROAD : $this->type;
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

  public function getLimit()
  {
    if ($this->road === HAS_ROAD) {
      return 8;
    } else {
      return $this->limit;
    }
  }

  public function getRemainingLimit($faction)
  {
    return $this->getLimit() - $this->getLimitUsed($faction);
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

  public function isPath()
  {
    return $this->getType() === PATH;
  }

  public function isPathOfIndianNation($nation) {
    return $this->indianNationPath === $nation;
  }

  public function canBeUsedByUnit($unit, $ignoreLimit = false)
  {
    return $this->canBeUsedByUnits([$unit], $ignoreLimit);
  }

  public function canBeUsedByUnits($units, $ignoreLimit = false)
  {
    if (!$ignoreLimit && $this->getRemainingLimit($units[0]->getFaction()) < count($units)) {
      return false;
    }

    $hasFleet = Utils::array_some($units, function ($unit) {
      return $unit->isFleet();
    });
    if ($hasFleet && !$this->isCoastalConnection()) {
      return false;
    }

    $requiresRoadOrHighway = Utils::array_some($units, function ($unit) {
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
    unset($data['location']);
    unset($data['state']);
    $data['type'] = $this->getType();
    return $data;
  }
}
