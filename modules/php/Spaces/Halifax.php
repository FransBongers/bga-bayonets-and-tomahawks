<?php
namespace BayonetsAndTomahawks\Spaces;

class Halifax extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = HALIFAX;
    $this->battlePriority = 32;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('HALIFAX');
    $this->victorySpace = true;
    $this->top = 570;
    $this-> left = 1085;
  }
}
