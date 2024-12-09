<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Preferences;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Events;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;

class CustomLogRecord extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $table = 'custom_log';
  protected $primary = 'id';

  protected $attributes = [
    'id' => ['id', 'int'],
    'round' => ['round', 'str'],
    'type' => ['type', 'str'],
    'year' => ['year', 'str'],
    'data' => ['data', 'obj'],
  ];


  public function jsonSerialize()
  {
    $data = parent::jsonSerialize();

    return array_merge(
      $data,
      [],
    );
  }

  public function getId()
  {
    return (int) parent::getId();
  }
}
