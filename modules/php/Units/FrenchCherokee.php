<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class FrenchCherokee extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FRENCH_CHEROKEE;
    $this->counterText = clienttranslate('CHEROKEE');
    $this->faction = FRENCH;
    $this->indian = true;
    $this->villages = [CHOTE, KEOWEE];
  }
}
