<?php
namespace BayonetsAndTomahawks\Units;

class Duquesne extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DUQUESNE;
    $this->counterText = clienttranslate('Duquesne');
    $this->faction = FRENCH;
  }
}
