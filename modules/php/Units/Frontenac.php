<?php
namespace BayonetsAndTomahawks\Units;

class Frontenac extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FRONTENAC;
    $this->counterText = clienttranslate('Frontenac');
    $this->faction = FRENCH;
  }
}
