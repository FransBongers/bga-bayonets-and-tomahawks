<?php
namespace BayonetsAndTomahawks\Spaces;

class Gennisheyo extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GENNISHEYO;
    $this->battlePriority = 192;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Gennisheyo');
    $this->victorySpace = false;
    $this->top = 1680;
    $this-> left = 491;
  }
}
