<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;

class Fleet extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FLEET;
    parent::__construct($row);
    $this->stackOrder = 1;
    $this->mpLimit = 2;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY, PATH]; // requires coastal
    $this->shape = CIRCLE;
  }

  public function eliminate($player)
  {
    $previousLocation = $this->getLocation();
    $this->setReduced(0);
    $this->setLocation(POOL_FLEETS);
    Notifications::eliminateUnit($player, $this, $previousLocation);
    GameMap::lastEliminatedUnitCheck($player, $previousLocation, $this->getFaction());
  }

  public function removeFromPool()
  {
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::removeFromPlay($this, POOL_FLEETS);
  }
}
