<?php
namespace BayonetsAndTomahawks\Units;

class DeLIsle extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DE_L_ISLE;
    $this->counterText = clienttranslate('DeLIsle');
    $this->faction = FRENCH;
  }
}
