<?php
namespace BayonetsAndTomahawks\Spaces;

class Mtan extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MTAN;
    $this->battlePriority = 21;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate("Mta'n");
    $this->victorySpace = false;
    $this->top = 455.5;
    $this->left = 385.5;
    $this->adjacentSpaces = [
      RIVIERE_DU_LOUP => MTAN_RIVIERE_DU_LOUP,
      RIVIERE_RISTIGOUCHE => MTAN_RIVIERE_RISTIGOUCHE,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }
}
