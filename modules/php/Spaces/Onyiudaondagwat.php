<?php
namespace BayonetsAndTomahawks\Spaces;

class Onyiudaondagwat extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONYIUDAONDAGWAT;
    $this->battlePriority = 173;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Onyiudaondagwat');
    $this->victorySpace = false;
    $this->top = 1620;
    $this-> left = 416.5;
  }
}
