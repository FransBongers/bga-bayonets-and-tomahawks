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
  }
}
