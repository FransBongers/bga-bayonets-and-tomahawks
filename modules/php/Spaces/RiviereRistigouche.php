<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereRistigouche extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_RISTIGOUCHE;
    $this->battlePriority = 23;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rivière Ristigouche');
    $this->victorySpace = false;
    $this->top = 484;
    $this->left = 533.5;
    $this->adjacentSpaces = [
      MIRAMICHY => MIRAMICHY_RIVIERE_RISTIGOUCHE,
      MTAN => MTAN_RIVIERE_RISTIGOUCHE,
    ];
  }
}
