<?php
namespace BayonetsAndTomahawks\Units;

class Lery extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LERY;
    $this->counterText = clienttranslate('Lery');
    $this->faction = FRENCH;
  }
}
