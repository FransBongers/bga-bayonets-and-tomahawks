<?php
namespace BayonetsAndTomahawks\Units;

class JacquesCartier extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = JACQUES_CARTIER;
    $this->counterText = clienttranslate('Jacques Cartier');
    $this->faction = FRENCH;
  }
}
