<?php
namespace BayonetsAndTomahawks\Spaces;

class York extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = YORK;
    $this->battlePriority = 111;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('YORK');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 1050;
    $this->left = 969.5;
    $this->adjacentSpaces = [
      BOSTON => BOSTON_YORK,
      RUMFORD => RUMFORD_YORK,
      ST_GEORGE => ST_GEORGE_YORK,
      TACONNET => TACONNET_YORK,
      ZAWAKWTEGOK => YORK_ZAWAKWTEGOK,
    ];
  }
}
