<?php
namespace BayonetsAndTomahawks\Spaces;

class Molojoak extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOLOJOAK;
    $this->battlePriority = 92;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Molôjoak');
    $this->victorySpace = false;
  }
}
