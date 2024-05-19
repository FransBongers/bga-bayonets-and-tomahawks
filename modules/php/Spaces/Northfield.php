<?php
namespace BayonetsAndTomahawks\Spaces;

class Northfield extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NORTHFIELD;
    $this->battlePriority = 141;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 3;
    $this->name = clienttranslate('NORTHFIELD');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 1286;
    $this->left = 897;
    $this->adjacentSpaces = [
      ALBANY => ALBANY_NORTHFIELD,
      BOSTON => BOSTON_NORTHFIELD,
      NEW_LONDON => NEW_LONDON_NORTHFIELD,
      NUMBER_FOUR => NORTHFIELD_NUMBER_FOUR,
      RUMFORD => NORTHFIELD_RUMFORD,
    ];
  }
}
