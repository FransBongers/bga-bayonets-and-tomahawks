<?php
namespace BayonetsAndTomahawks\Spaces;

class Miramichy extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIRAMICHY;
    $this->battlePriority = 31;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('Miramichy');
    $this->victorySpace = true;
    $this->top = 501;
    $this->left = 725.5;
    $this->adjacentSpaces = [
      CHIGNECTOU => CHIGNECTOU_MIRAMICHY,
      GRAND_SAULT => GRAND_SAULT_MIRAMICHY,
      POINTE_SAINTE_ANNE => MIRAMICHY_POINTE_SAINTE_ANNE,
      PORT_LA_JOYE => MIRAMICHY_PORT_LA_JOYE,
      RIVIERE_RISTIGOUCHE => MIRAMICHY_RIVIERE_RISTIGOUCHE,
    ];
  }
}
