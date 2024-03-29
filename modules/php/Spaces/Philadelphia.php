<?php
namespace BayonetsAndTomahawks\Spaces;

class Philadelphia extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = PHILADELPHIA;
    $this->battlePriority = 999;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('Philadelphia');
    $this->victorySpace = false;
    $this->top = 1834;
    $this-> left = 1077;
  }
}
