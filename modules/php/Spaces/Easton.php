<?php
namespace BayonetsAndTomahawks\Spaces;

class Easton extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = EASTON;
    $this->battlePriority = 203;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Easton');
    $this->victorySpace = false;
  }
}
