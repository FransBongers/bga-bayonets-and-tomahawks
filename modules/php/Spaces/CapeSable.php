<?php
namespace BayonetsAndTomahawks\Spaces;

class CapeSable extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAPE_SABLE;
    $this->battlePriority = 71;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('CapeSable');
    $this->victorySpace = false;
    $this->top = 738;
    $this-> left = 1092;
  }
}
