<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class Iroquois extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = IROQUOIS;
    $this->counterText = clienttranslate('IROQUOIS');
    $this->faction = NEUTRAL;
    $this->indian = true;
    $this->villages = [ONONTAKE, OQUAGA, KAHNISTIOH];
  }

  public function getFaction()
  {
    return Globals::getControlIroquois();
  }
}
