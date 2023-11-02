<?php
namespace BayonetsAndTomahawks\Units;

class Putnam extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = PUTNAM;
    $this->counterText = clienttranslate('Putnam');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
