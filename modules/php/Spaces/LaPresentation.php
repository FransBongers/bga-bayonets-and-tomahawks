<?php
namespace BayonetsAndTomahawks\Spaces;

class LaPresentation extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESENTATION;
    $this->battlePriority = 151;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('La Présentation');
    $this->victorySpace = false;
    $this->top = 1371.5;
    $this-> left = 286;
  }
}
