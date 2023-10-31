<?php
namespace BayonetsAndTomahawks\Spaces;

class Kwanoskwamcok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KWANOSKWANCOK;
    $this->battlePriority = 73;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kwanoskwamcok');
    $this->victorySpace = false;
  }
}
