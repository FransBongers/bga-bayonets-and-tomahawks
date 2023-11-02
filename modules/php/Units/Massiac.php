<?php
namespace BayonetsAndTomahawks\Units;

class Massiac extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MASSIAC;
    $this->counterText = clienttranslate('Massiac');
    $this->faction = FRENCH;
  }
}
