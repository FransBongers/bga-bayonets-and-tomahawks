<?php
namespace BayonetsAndTomahawks\Spaces;

class Sachendaga extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SACHENDAGA;
    $this->battlePriority = 143;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate("Sachenda'ga");
    $this->victorySpace = false;
    $this->top = 1331.5;
    $this-> left = 536.5;
  }
}
