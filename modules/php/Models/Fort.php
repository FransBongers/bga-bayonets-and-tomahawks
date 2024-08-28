<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

class Fort extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FORT;
    parent::__construct($row);
    $this->stackOrder = 0;
  }

  public function eliminate($player)
  {
    $previousLocation = $this->getLocation();
    $this->setReduced(0);
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::eliminateUnit($player, $this, $previousLocation);
  }
}
