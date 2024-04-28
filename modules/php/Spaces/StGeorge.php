<?php
namespace BayonetsAndTomahawks\Spaces;

class StGeorge extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ST_GEORGE;
    $this->battlePriority = 82;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('St. George');
    $this->victorySpace = false;
    $this->top = 868.5;
    $this->left = 935;
    $this->adjacentSpaces = [
      KADESQUIT => KADESQUIT_ST_GEORGE,
      KWANOSKWAMCOK => KWANOSKWAMCOK_ST_GEORGE,
      TACONNET => ST_GEORGE_TACONNET,
      YORK => ST_GEORGE_YORK,
    ];
  }
}
