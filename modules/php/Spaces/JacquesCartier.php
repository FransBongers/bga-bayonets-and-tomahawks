<?php
namespace BayonetsAndTomahawks\Spaces;

class JacquesCartier extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = JACQUES_CARTIER;
    $this->battlePriority = 93;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Jacques Cartier');
    $this->victorySpace = false;
    $this->top = 926.5;
    $this-> left = 233.5;
  }
}
