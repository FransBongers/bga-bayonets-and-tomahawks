<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereRistigouche extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_RISTIGOUCHE;
    $this->battlePriority = 23;
    $this->colony = ACADIE;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Rivière Ristigouche');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 484;
    $this->left = 533.5;
    $this->adjacentSpaces = [
      MIRAMICHY => MIRAMICHY_RIVIERE_RISTIGOUCHE,
      MTAN => MTAN_RIVIERE_RISTIGOUCHE,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }
}
