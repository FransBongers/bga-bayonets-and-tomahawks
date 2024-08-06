<?php

namespace BayonetsAndTomahawks\Units;

class Seneca extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = SENECA;
    $this->counterText = clienttranslate('Seneca');
    $this->faction = FRENCH;
    $this->indian = true;
    $this->villages = [GENNISHEYO];
  }
}
