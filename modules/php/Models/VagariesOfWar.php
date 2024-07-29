<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

class VagariesOfWar extends AbstractUnit
{
  protected $putTokenBackInPool = false;

  public function __construct($row)
  {
    $this->type = VAGARIES_OF_WAR;
    parent::__construct($row);
  }

  public function getPutTokenBackInPool()
  {
    return $this->putTokenBackInPool;
  }

  public function removeFromPlay()
  {
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::removeFromPlay($this);
  }

  public function returnToPool($pool)
  {
    $this->setLocation($pool);
    Notifications::returnToPool($this);
  }
}
