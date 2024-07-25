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
}
