<?php
namespace BayonetsAndTomahawks\Units;

class AubryBelestre extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = AUBRY_BELESTRE;
    $this->counterText = clienttranslate('Aubry Belestre');
    $this->faction = FRENCH;
  }
}
