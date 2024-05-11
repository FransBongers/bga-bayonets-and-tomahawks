<?php
namespace BayonetsAndTomahawks\Spaces;

class LaPresentation extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESENTATION;
    $this->battlePriority = 151;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('La Présentation');
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 1371.5;
    $this->left = 286;
    $this->adjacentSpaces = [
      BAYE_DE_CATARACOUY => BAYE_DE_CATARACOUY_LA_PRESENTATION,
      KAHUAHGO => KAHUAHGO_LA_PRESENTATION,
      MONTREAL => LA_PRESENTATION_MONTREAL,
      NIHANAWATE => LA_PRESENTATION_NIHANAWATE,
    ];
  }
}
