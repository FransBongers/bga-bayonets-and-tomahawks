<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class Cherokee extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CHEROKEE;
    $this->counterText = clienttranslate('CHEROKEE');
    $this->faction = NEUTRAL;
    $this->indian = true;
    $this->villages = [CHOTE, KEOWEE];
  }

  public function getFaction()
  {
    return Globals::getControlCherokee();
  }
}
