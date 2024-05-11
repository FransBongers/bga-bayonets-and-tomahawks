<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereDuLoup extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_DU_LOUP;
    $this->battlePriority = 41;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Rivière du Loup');
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 616;
    $this->left = 396;
    $this->adjacentSpaces = [
      COTE_DU_SUD => COTE_DU_SUD_RIVIERE_DU_LOUP,
      MATAWASKIYAK => MATAWASKIYAK_RIVIERE_DU_LOUP,
      MTAN => MTAN_RIVIERE_DU_LOUP,
    ];
  }
}
