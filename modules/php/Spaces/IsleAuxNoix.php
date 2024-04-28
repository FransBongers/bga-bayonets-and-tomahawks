<?php
namespace BayonetsAndTomahawks\Spaces;

class IsleAuxNoix extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX;
    $this->battlePriority = 113;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Isle aux Noix');
    $this->victorySpace = false;
    $this->top = 1102;
    $this->left = 456;
    $this->adjacentSpaces = [
      LES_TROIS_RIVIERES => ISLE_AUX_NOIX_LES_TROIS_RIVIERES,
      MAMHLAWBAGOK => ISLE_AUX_NOIX_MAMHLAWBAGOK,
      MONTREAL => ISLE_AUX_NOIX_MONTREAL,
      SARANAC => ISLE_AUX_NOIX_SARANAC,
      TICONDEROGA => ISLE_AUX_NOIX_TICONDEROGA,
    ];
  }
}
