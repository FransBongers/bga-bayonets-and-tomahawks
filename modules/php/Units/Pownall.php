<?php
namespace BayonetsAndTomahawks\Units;

class Pownall extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = POWNALL;
    $this->counterText = clienttranslate('Pownall');
    $this->faction = BRITISH;
  }
}
