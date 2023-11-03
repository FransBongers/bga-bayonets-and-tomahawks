<?php
namespace BayonetsAndTomahawks\Spaces;

class FortOuiatenon extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORT_OUIATENON;
    $this->battlePriority = 282;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Fort Ouiatenon');
    $this->victorySpace = false;
    $this->top = 2174;
    $this-> left = 300.5;
  }
}
