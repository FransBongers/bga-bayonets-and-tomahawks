<?php
namespace BayonetsAndTomahawks\Spaces;

class Mikazawitegok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIKAZAWITEGOK;
    $this->battlePriority = 133;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mikazawitegok');
    $this->victorySpace = false;
    $this->top = 1260;
    $this-> left = 741;
  }
}
