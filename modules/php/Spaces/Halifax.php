<?php
namespace BayonetsAndTomahawks\Spaces;

class Halifax extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = HALIFAX;
    $this->battlePriority = 32;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 2;
    $this->name = clienttranslate('HALIFAX');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 570;
    $this->left = 1085;
    $this->adjacentSpaces = [
      ANNAPOLIS_ROYAL => ANNAPOLIS_ROYAL_HALIFAX,
      CAPE_SABLE => CAPE_SABLE_HALIFAX,
      CHIGNECTOU => CHIGNECTOU_HALIFAX,
    ];
  }
}
