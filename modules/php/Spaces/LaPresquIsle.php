<?php
namespace BayonetsAndTomahawks\Spaces;

class LaPresquIsle extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESQU_ISLE;
    $this->battlePriority = 222;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate("La Presqu'Isle");
    $this->victorySpace = false;
    $this->top = 1874;
    $this-> left = 468;
  }
}
