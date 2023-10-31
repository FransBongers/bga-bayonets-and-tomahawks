<?php
namespace BayonetsAndTomahawks\Spaces;

class Mamhlawbagok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MAMHLAWBAGOK;
    $this->battlePriority = 103;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mamhlawbagok');
    $this->victorySpace = false;
  }
}
