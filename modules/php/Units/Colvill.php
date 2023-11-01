<?php
namespace BayonetsAndTomahawks\Units;

class Colvill extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = COLVILL;
    $this->counterText = clienttranslate('Colvill');
    $this->faction = BRITISH;
  }
}
