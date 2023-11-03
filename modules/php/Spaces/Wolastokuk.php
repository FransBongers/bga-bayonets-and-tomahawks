<?php
namespace BayonetsAndTomahawks\Spaces;

class Wolastokuk extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WOLASTOKUK;
    $this->battlePriority = 63;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Wolastokuk');
    $this->victorySpace = false;
    $this->top = 732;
    $this-> left = 579;
  }
}
