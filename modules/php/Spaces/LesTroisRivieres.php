<?php
namespace BayonetsAndTomahawks\Spaces;

class LesTroisRivieres extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_TROIS_RIVIERES;
    $this->battlePriority = 101;
    $this->colony = CANADA;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 2;
    $this->name = clienttranslate('Les Trois Rivières');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 1009.5;
    $this->left = 369.5;
    $this->adjacentSpaces = [
      ISLE_AUX_NOIX => ISLE_AUX_NOIX_LES_TROIS_RIVIERES,
      JACQUES_CARTIER => JACQUES_CARTIER_LES_TROIS_RIVIERES,
      MAMHLAWBAGOK => LES_TROIS_RIVIERES_MAMHLAWBAGOK,
      MONTREAL => LES_TROIS_RIVIERES_MONTREAL,
      QUEBEC => LES_TROIS_RIVIERES_QUEBEC,
    ];
  }
}
