<?php
namespace BayonetsAndTomahawks\Units;

class Dunn extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DUNN;
    $this->counterText = clienttranslate('Dunn');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
