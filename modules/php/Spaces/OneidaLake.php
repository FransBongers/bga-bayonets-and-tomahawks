<?php
namespace BayonetsAndTomahawks\Spaces;

class OneidaLake extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONEIDA_LAKE;
    $this->battlePriority = 163;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Oneida Lake');
    $this->victorySpace = false;
    $this->top = 1520;
    $this-> left = 599.5;
  }
}
