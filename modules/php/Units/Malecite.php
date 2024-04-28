<?php
namespace BayonetsAndTomahawks\Units;

class Malecite extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MICMAC;
    $this->counterText = clienttranslate('Malécite');
    $this->faction = INDIAN;
    $this->indian = true;
  }
}
