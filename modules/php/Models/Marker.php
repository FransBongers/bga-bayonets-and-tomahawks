<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class Marker extends \BayonetsAndTomahawks\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'markers';
  protected $primary = 'marker_id';
  protected $attributes = [
    'id' => ['marker_id', 'str'],
    'location' => ['marker_location', 'str'],
    'state' => ['marker_location', 'int'],
    // 'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $location = null;
  protected $state = null;

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'location' => $this->location,
      'type' => $this->id,
      'side' => $this->state === 0 ? 'front' : 'back',
      'manager' => MARKERS,
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
}
