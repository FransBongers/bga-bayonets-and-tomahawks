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
    'side' => ['side', 'int'],
    'state' => ['marker_state', 'int'],
    // 'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $location = null;
  protected $state = null;
  protected $side = null;
  protected $stackOrder = 11;

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'location' => $this->location,
      'type' => $this->getType(),
      'side' => $this->side === 0 ? 'front' : 'back',
      'manager' => MARKERS,
      'stackOrder' => $this->stackOrder,
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

  public function getType()
  {
    // TODO: rename IDs so we can explode for all markers?
    if (in_array($this->id,NAMED_MARKERS)) {
      return $this->id;
    } else {
      return explode('_', $this->id)[0];
    }
  }

  public function remove($player)
  {
    $previousLocation = $this->getLocation();
    $this->setLocation(Locations::markerSupply($this->getType()));
    Notifications::removeMarkerFromStack($player, $this, $previousLocation);
  }
}
