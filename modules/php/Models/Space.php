<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

/**
 * Space
 */
class Space extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $table = 'spaces';
  protected $primary = 'space_id';
  protected $attributes = [
    'id' => ['space_id', 'int'],
    'location' => 'space_location',
    'state' => ['space_state', 'int'],
    'control' => ['control', 'str'],
    'extraDatas' => ['extra_datas', 'obj'],
  ];
  protected $staticAttributes = ['name', 'isVictorySpace'];

  protected $id = null;
  protected $control = null;
  protected $faction = null;
  protected $location = null;
  protected $name = null;
  protected $isVictorySpace = false;

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
      'isVictorySpace' => $this->isVictorySpace,
      'name' =>$this->name,
    ];
  }
}