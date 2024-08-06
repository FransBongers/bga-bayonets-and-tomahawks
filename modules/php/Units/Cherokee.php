<?php

namespace BayonetsAndTomahawks\Units;

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
}
