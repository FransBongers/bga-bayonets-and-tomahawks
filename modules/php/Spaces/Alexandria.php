<?php
namespace BayonetsAndTomahawks\Spaces;

class Alexandria extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALEXANDRIA;
    $this->battlePriority = 999;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('Alexandria');
    $this->victorySpace = false;
    $this->top = 2064;
    $this-> left = 1198.5;
  }
}
