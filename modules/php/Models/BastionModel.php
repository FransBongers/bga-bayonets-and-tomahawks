<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;

class BastionModel extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = BASTION_UNIT_TYPE;
    parent::__construct($row);
  }

  public function eliminate($player)
  {
    $previousLocation = $this->getLocation();
    // Need to check for the actual location. Not the spot the Basion is in.
    $previousLocationForCheck = str_replace(['Bastion1', 'Bastion2'], '', $this->getLocation());
    $this->setReduced(0);
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::eliminateUnit($player, $this, $previousLocation);
    GameMap::lastEliminatedUnitCheck($player, $previousLocationForCheck, $this->getFaction());
  }
}
