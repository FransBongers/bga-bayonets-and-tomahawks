<?php

namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Globals;

class Keowee extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KEOWEE;
    $this->battlePriority = 293;
    $this->defaultControl = INDIAN;
    $this->indianVillage = CHEROKEE;
    $this->name = clienttranslate('Keowee');
    $this->victorySpace = false;
    $this->top = 2224;
    $this->left = 1065.5;
    $this->adjacentSpaces = [
      CHOTE => CHOTE_KEOWEE,
      NINETY_SIX => KEOWEE_NINETY_SIX,
    ];
  }

  public function getDefaultControl()
  {
    $control = Globals::getControlCherokee();
    if ($control === NEUTRAL) {
      return NEUTRAL;
    } else {
      return $control;
    }
  }
}
