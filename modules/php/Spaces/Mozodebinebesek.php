<?php
namespace BayonetsAndTomahawks\Spaces;

class Mozodebinebesek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOZODEBINEBESEK;
    $this->battlePriority = 74;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mozôdebinebesek');
    $this->victorySpace = false;
    $this->top = 792;
    $this-> left = 661;
  }
}
