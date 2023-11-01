<?php
namespace BayonetsAndTomahawks\Units;

class Goreham extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = GOREHAM;
    $this->counterText = clienttranslate('Goreham');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
