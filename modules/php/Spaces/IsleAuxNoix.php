<?php
namespace BayonetsAndTomahawks\Spaces;

class IsleAuxNoix extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX;
    $this->battlePriority = 113;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Isle aux Noix');
    $this->victorySpace = false;
    $this->top = 1102;
    $this-> left = 456;
  }
}
