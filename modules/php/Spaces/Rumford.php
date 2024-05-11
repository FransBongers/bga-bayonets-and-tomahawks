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
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Rumford');
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 1121.5;
    $this->left = 895.5;
    $this->adjacentSpaces = [
      NORTHFIELD => NORTHFIELD_RUMFORD,
      YORK => RUMFORD_YORK,
      ZAWAKWTEGOK => RUMFORD_ZAWAKWTEGOK,
    ];
  }
}
