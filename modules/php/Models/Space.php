<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Units;

/**
 * Space
 */
class Space extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $table = 'spaces';
  protected $primary = 'space_id';
  protected $attributes = [
    'id' => ['space_id', 'int'],
    'control' => ['control', 'str'],
    // 'extraData' => ['extra_data', 'obj'],
  ];
  protected $staticAttributes = ['battlePriority', 'name', 'victorySpace', 'defaultControl', 'top', 'left'];

  protected $id = null;
  protected $battlePriority;
  protected $control = null;
  protected $defaultControl;
  protected $faction = null;
  protected $name = null;
  protected $victorySpace = false;
  protected $top = 0;
  protected $left = 0;
  protected $adjacentSpaces = [];

  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'control' => $this->control,
      'defaultControl' => $this->defaultControl,
      'name' => $this->name,
      'victorySpace' => $this->victorySpace,
      'top' => $this->top,
      'left' => $this->left,
    ];
  }

  public function getUnits()
  {
    return Units::getInLocation($this->id)->toArray();
  }
}