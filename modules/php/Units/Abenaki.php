<?php

namespace BayonetsAndTomahawks\Units;

class Abenaki extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ABENAKI;
    $this->counterText = clienttranslate('Abénaki');
    $this->faction = FRENCH;
    $this->indian = true;
    $this->villages = [LES_TROIS_RIVIERES];
  }
}
