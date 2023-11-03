<?php
namespace BayonetsAndTomahawks\Spaces;

class Kithanink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KITHANINK;
    $this->battlePriority = 233;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kithanink');
    $this->victorySpace = false;
    $this->top = 1923;
    $this-> left = 605;
  }
}
