<?php
namespace BayonetsAndTomahawks\Spaces;

class Rumford extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RUMFORD;
    $this->battlePriority = 121;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 2;
    $this->name = clienttranslate('Rumford');
    $this->settledSpace = true;
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
