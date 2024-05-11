<?php
namespace BayonetsAndTomahawks\Spaces;

class JacquesCartier extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = JACQUES_CARTIER;
    $this->battlePriority = 93;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Jacques Cartier');
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 926.5;
    $this->left = 233.5;
    $this->adjacentSpaces = [
      COTE_DE_BEAUPRE => COTE_DE_BEAUPRE_JACQUES_CARTIER,
      LES_TROIS_RIVIERES => JACQUES_CARTIER_LES_TROIS_RIVIERES,
      QUEBEC => JACQUES_CARTIER_QUEBEC,
    ];
  }
}
