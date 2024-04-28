<?php
namespace BayonetsAndTomahawks\Spaces;

class Ticonderoga extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TICONDEROGA;
    $this->battlePriority = 131;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('TICONDEROGA');
    $this->victorySpace = true;
    $this->top = 1206.5;
    $this->left = 591.5;
    $this->adjacentSpaces = [
      GOASEK => GOASEK_TICONDEROGA,
      ISLE_AUX_NOIX => ISLE_AUX_NOIX_TICONDEROGA,
      LAKE_GEORGE => LAKE_GEORGE_TICONDEROGA,
      NUMBER_FOUR => NUMBER_FOUR_TICONDEROGA,
      SARANAC => SARANAC_TICONDEROGA,
    ];
  }
}
