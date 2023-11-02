<?php
namespace BayonetsAndTomahawks\Units;

class Armstrong extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ARMSTRONG;
    $this->counterText = clienttranslate('Armstrong');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
