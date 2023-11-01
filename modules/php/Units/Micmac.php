<?php
namespace BayonetsAndTomahawks\Units;

class Micmac extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MICMAC;
    $this->counterText = clienttranslate('Micmac');
    $this->faction = FRENCH;
    $this->indian = true;
  }
}
