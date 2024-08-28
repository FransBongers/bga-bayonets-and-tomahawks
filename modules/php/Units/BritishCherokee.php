<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class BritishCherokee extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BRITISH_CHEROKEE;
    $this->counterText = clienttranslate('CHEROKEE');
    $this->faction = BRITISH;
    $this->indian = true;
    $this->villages = [CHOTE, KEOWEE];
  }

  public function getFaction()
  {
    return Globals::getControlCherokee();
  }
}
