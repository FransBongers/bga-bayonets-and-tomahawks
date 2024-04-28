<?php
namespace BayonetsAndTomahawks\Spaces;

class Saranac extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SARANAC;
    $this->battlePriority = 132;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Saranac');
    $this->victorySpace = false;
    $this->top = 1233.5;
    $this->left = 437;
    $this->adjacentSpaces = [
      ISLE_AUX_NOIX => ISLE_AUX_NOIX_SARANAC,
      NIHANAWATE => NIHANAWATE_SARANAC,
      TICONDEROGA => SARANAC_TICONDEROGA,
    ];
  }
}
