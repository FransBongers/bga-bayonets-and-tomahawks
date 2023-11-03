<?php
namespace BayonetsAndTomahawks\Spaces;

class Kingston extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KINGSTON;
    $this->battlePriority = 171;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kingston');
    $this->victorySpace = false;
    $this->top = 1533;
    $this-> left = 891.5;
  }
}
