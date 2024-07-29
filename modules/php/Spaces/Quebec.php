<?php

namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Managers\Units;

class Quebec extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = QUEBEC;
    $this->battlePriority = 81;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 3;
    $this->name = clienttranslate('QUÉBEC');
    $this->settledSpace = true;
    $this->value = 3;
    $this->victorySpace = true;
    $this->top = 863.5;
    $this->left = 428.5;
    $this->adjacentSpaces = [
      COTE_DE_BEAUPRE => COTE_DE_BEAUPRE_QUEBEC,
      COTE_DU_SUD => COTE_DU_SUD_QUEBEC,
      JACQUES_CARTIER => JACQUES_CARTIER_QUEBEC,
      LES_TROIS_RIVIERES => LES_TROIS_RIVIERES_QUEBEC,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }

  public function hasBastion()
  {
    return count(Units::getAll(QUEBEC_BASTION_1)) + count(Units::getAll(QUEBEC_BASTION_2)) > 0;
  }
}
