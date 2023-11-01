<?php
namespace BayonetsAndTomahawks\Units;

class Carillon extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CARILLON;
    $this->counterText = clienttranslate('Carillon');
    $this->faction = FRENCH;
  }
}
