<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

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
    $this->setReduced(0);
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::eliminateUnit($player, $this, $previousLocation);
  }

}
