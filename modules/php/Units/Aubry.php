<?php
namespace BayonetsAndTomahawks\Units;

class Aubry extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = AUBRY;
    $this->counterText = clienttranslate('Aubry');
    $this->faction = FRENCH;
  }
}
