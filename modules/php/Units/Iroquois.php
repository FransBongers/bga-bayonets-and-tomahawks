<?php

namespace BayonetsAndTomahawks\Units;

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
}
