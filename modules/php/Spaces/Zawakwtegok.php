<?php
namespace BayonetsAndTomahawks\Spaces;

class Zawakwtegok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ZAWAKWTEGOK;
    $this->battlePriority = 102;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Zawakwtegok');
    $this->victorySpace = false;
    $this->top = 994.5;
    $this->left = 812;
    $this->adjacentSpaces = [
      GOASEK => GOASEK_ZAWAKWTEGOK,
      MOLOJOAK => MOLOJOAK_ZAWAKWTEGOK,
      NUMBER_FOUR => NUMBER_FOUR_ZAWAKWTEGOK,
      RUMFORD => RUMFORD_ZAWAKWTEGOK,
      YORK => YORK_ZAWAKWTEGOK,
    ];
  }
}
