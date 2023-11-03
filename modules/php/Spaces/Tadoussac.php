<?php
namespace BayonetsAndTomahawks\Spaces;

class Tadoussac extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TADOUSSAC;
    $this->battlePriority = 42;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Tadoussac');
    $this->victorySpace = false;
    $this->top = 634;
    $this-> left = 280.5;
  }
}
