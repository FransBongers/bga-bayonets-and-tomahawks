<?php
namespace BayonetsAndTomahawks\Spaces;

class Diiohage extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE;
    $this->battlePriority = 251;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Diiohage');
    $this->victorySpace = false;
    $this->top = 2003;
    $this-> left = 494;
  }
}
