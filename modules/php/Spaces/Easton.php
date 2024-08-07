<?php
namespace BayonetsAndTomahawks\Spaces;

class Easton extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = EASTON;
    $this->battlePriority = 203;
    $this->colony = PENNSYLVANIA_AND_DELAWARE;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 2;
    $this->name = clienttranslate('Easton');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 1780.5;
    $this->left = 937.5;
    $this->adjacentSpaces = [
      CARLISLE => CARLISLE_EASTON,
      GNADENHUTTEN => EASTON_GNADENHUTTEN,
      MINISINK => EASTON_MINISINK,
      PHILADELPHIA => EASTON_PHILADELPHIA,
    ];
  }
}
