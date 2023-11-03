<?php
namespace BayonetsAndTomahawks\Spaces;

class Mekekasink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MEKEKASINK;
    $this->battlePriority = 263;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mekekasink');
    $this->victorySpace = false;
    $this->top = 2077;
    $this-> left = 806;
  }
}
