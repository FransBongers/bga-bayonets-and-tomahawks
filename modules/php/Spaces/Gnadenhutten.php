<?php
namespace BayonetsAndTomahawks\Spaces;

class Gnadenhutten extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN;
    $this->battlePriority = 202;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Gnadenhütten');
    $this->victorySpace = false;
    $this->top = 1739;
    $this-> left = 773.5;
  }
}
