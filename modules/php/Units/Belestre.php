<?php
namespace BayonetsAndTomahawks\Units;

class Belestre extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BELESTRE;
    $this->counterText = clienttranslate('Belestre');
    $this->faction = FRENCH;
  }
}
