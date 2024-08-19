<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class WarInEuropeChit extends \BayonetsAndTomahawks\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'wie_chits';
  protected $primary = 'wie_chit_id';
  protected $attributes = [
    'id' => ['wie_chit_id', 'str'],
    'location' => ['wie_chit_location', 'str'],
    'state' => ['wie_chit_state', 'int'],
    'value' => ['value', 'int'],
    // 'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $location = null;
  protected $state = null;
  protected $value = 0;

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'location' => $this->location,
      'value' => $this->value,
      'revealed' => $this->isRevealed(),
    ];
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.


  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function isRevealed()
  {
    return $this->state === 1; // use other prop instead of state?
  }

  public function setRevealed($value)
  {
    $this->setState($value);
  }
}
