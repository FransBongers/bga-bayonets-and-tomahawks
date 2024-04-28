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
    $this->top = 1780;
    $this->left = 937.5;
    $this->adjacentSpaces = [
      CARLISLE => CARLISLE_EASTON,
      GNADENHUTTEN => EASTON_GNADENHUTTEN,
      MINISINK => EASTON_MINISINK,
      PHILADELPHIA => EASTON_PHILADELPHIA,
    ];
  }
}
