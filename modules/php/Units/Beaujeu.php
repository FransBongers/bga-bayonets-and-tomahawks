<?php
namespace BayonetsAndTomahawks\Units;

class Beaujeu extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEAUJEU;
    $this->counterText = clienttranslate('Beaujeu');
    $this->faction = FRENCH;
  }
}
