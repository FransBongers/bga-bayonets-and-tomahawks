<?php
namespace BayonetsAndTomahawks\Spaces;

class Taconnet extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TACONNET;
    $this->battlePriority = 91;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Taconnet');
    $this->victorySpace = false;
  }
}
