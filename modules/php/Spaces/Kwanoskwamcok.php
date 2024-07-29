<?php
namespace BayonetsAndTomahawks\Spaces;

class Kwanoskwamcok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KWANOSKWAMCOK;
    $this->battlePriority = 73;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kwanoskwamcok');
    $this->victorySpace = false;
    $this->top = 754;
    $this->left = 926.5;
    $this->adjacentSpaces = [
      CHIGNECTOU => CHIGNECTOU_KWANOSKWAMCOK,
      ST_GEORGE => KWANOSKWAMCOK_ST_GEORGE,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
