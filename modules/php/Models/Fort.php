<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

class Fort extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FORT;
    parent::__construct($row);
  }

  public function eliminate($player)
  {
    $this->setState(0);
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::eliminateUnit($player, $this);
  }
}
