<?php
namespace BayonetsAndTomahawks\Spaces;

class Mtan extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MTAN;
    $this->battlePriority = 21;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate("Mta'n");
    $this->victorySpace = false;
  }
}
