<?php
namespace BayonetsAndTomahawks\Spaces;

class StGeorge extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ST_GEORGE;
    $this->battlePriority = 82;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('St. George');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 868.5;
    $this->left = 935;
    $this->adjacentSpaces = [
      KADESQUIT => KADESQUIT_ST_GEORGE,
      KWANOSKWAMCOK => KWANOSKWAMCOK_ST_GEORGE,
      TACONNET => ST_GEORGE_TACONNET,
      YORK => ST_GEORGE_YORK,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
