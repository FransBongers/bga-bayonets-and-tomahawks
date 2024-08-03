<?php
namespace BayonetsAndTomahawks\Spaces;

class Minisink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MINISINK;
    $this->battlePriority = 182;
    $this->colony = NEW_YORK_AND_NEW_JERSEY;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 2;
    $this->name = clienttranslate('Minisink');
    $this->settledSpace = true;
    $this->value = 2;
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
