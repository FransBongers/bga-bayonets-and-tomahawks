<?php
namespace BayonetsAndTomahawks\Spaces;

class Montreal extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MONTREAL;
    $this->battlePriority = 122;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('MONTRÉAL');
    $this->victorySpace = true;
    $this->top = 1168.5;
    $this->left = 322;
    $this->adjacentSpaces = [
      ISLE_AUX_NOIX => ISLE_AUX_NOIX_MONTREAL,
      LA_PRESENTATION => LA_PRESENTATION_MONTREAL,
      LES_TROIS_RIVIERES => LES_TROIS_RIVIERES_MONTREAL,
    ];
  }
}
