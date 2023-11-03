<?php
namespace BayonetsAndTomahawks\Spaces;

class Ouentironk extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OUENTIRONK;
    $this->battlePriority = 193;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Ouentironk');
    $this->victorySpace = false;
    $this->top = 1697;
    $this-> left = 142;
  }
}
