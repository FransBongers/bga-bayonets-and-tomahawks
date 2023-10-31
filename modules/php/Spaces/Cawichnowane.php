<?php
namespace BayonetsAndTomahawks\Spaces;

class Cawichnowane extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE;
    $this->battlePriority = 213;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Cawichnowane');
    $this->victorySpace = false;
  }
}
