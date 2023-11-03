<?php
namespace BayonetsAndTomahawks\Spaces;

class Rumford extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RUMFORD;
    $this->battlePriority = 121;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rumford');
    $this->victorySpace = false;
    $this->top = 1121.5;
    $this-> left = 895.5;
  }
}
