<?php
namespace BayonetsAndTomahawks\Spaces;

class Mamhlawbagok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MAMHLAWBAGOK;
    $this->battlePriority = 103;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mamhlawbagok');
    $this->victorySpace = false;
    $this->top = 1012.5;
    $this->left = 550.5;
    $this->adjacentSpaces = [
      GOASEK => GOASEK_MAMHLAWBAGOK,
      ISLE_AUX_NOIX => ISLE_AUX_NOIX_MAMHLAWBAGOK,
      LES_TROIS_RIVIERES => LES_TROIS_RIVIERES_MAMHLAWBAGOK,
      NAMASKONKIK => MAMHLAWBAGOK_NAMASKONKIK,
    ];
  }
}
