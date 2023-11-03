<?php
namespace BayonetsAndTomahawks\Spaces;

class Winchester extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WINCHESTER;
    $this->battlePriority = 243;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Winchester');
    $this->victorySpace = false;
    $this->top = 2005;
    $this-> left = 1016;
  }
}
