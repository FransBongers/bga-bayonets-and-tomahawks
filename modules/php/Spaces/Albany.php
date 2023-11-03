<?php
namespace BayonetsAndTomahawks\Spaces;

class Albany extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY;
    $this->battlePriority = 161;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('ALBANY');
    $this->victorySpace = true;
    $this->top = 1440;
    $this-> left = 787;
  }
}
