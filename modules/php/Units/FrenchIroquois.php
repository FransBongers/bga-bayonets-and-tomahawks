<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class FrenchIroquois extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FRENCH_IROQUOIS;
    $this->counterText = clienttranslate('IROQUOIS');
    $this->faction = FRENCH;
    $this->indian = true;
    $this->villages = [ONONTAKE, OQUAGA, KAHNISTIOH];
  }
}
