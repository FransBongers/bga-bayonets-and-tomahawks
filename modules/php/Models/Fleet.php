<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;

class Fleet extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FLEET;
    parent::__construct($row);
    $this->mpLimit = 2;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY, PATH]; // requires coastal
  }

  public function eliminate($player)
  {
    $this->setState(0);
    $this->setLocation(POOL_FLEETS);
    Notifications::eliminateUnit($player, $this);
  }

  public function removeFromPool()
  {
    $this->setLocation(REMOVED_FROM_PLAY, POOL_FLEETS);
    Notifications::removeFromPlay($this);
  }
}
