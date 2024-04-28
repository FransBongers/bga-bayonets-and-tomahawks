<?php
namespace BayonetsAndTomahawks\Spaces;

class Minisink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MINISINK;
    $this->battlePriority = 182;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Minisink');
    $this->victorySpace = false;
    $this->top = 1639;
    $this->left = 917.5;
    $this->adjacentSpaces = [
      EASTON => EASTON_MINISINK,
      GNADENHUTTEN => GNADENHUTTEN_MINISINK,
      KINGSTON => KINGSTON_MINISINK,
      OQUAGA => MINISINK_OQUAGA,
    ];
  }
}
