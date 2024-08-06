<?php

namespace BayonetsAndTomahawks\Units;

class Chaouanon extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CHAOUANON;
    $this->counterText = clienttranslate('Chaouanon');
    $this->faction = FRENCH;
    $this->indian = true;
    $this->villages = [FORKS_OF_THE_OHIO];
  }
}
