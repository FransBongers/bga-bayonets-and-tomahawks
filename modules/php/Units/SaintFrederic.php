<?php
namespace BayonetsAndTomahawks\Units;

class SaintFrederic extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = SAINT_FREDERIC;
    $this->counterText = clienttranslate('Saint Frédéric');
    $this->faction = FRENCH;
  }
}
