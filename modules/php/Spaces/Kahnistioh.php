<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Globals;

class Kahnistioh extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHNISTIOH;
    $this->battlePriority = 211;
    $this->defaultControl = INDIAN;
    $this->indianVillage = IROQUOIS;
    $this->name = clienttranslate('Kahnistioh');
    $this->victorySpace = false;
    $this->top = 1785;
    $this->left = 530;
    $this->adjacentSpaces = [
      CAWICHNOWANE => CAWICHNOWANE_KAHNISTIOH,
      GENNISHEYO => GENNISHEYO_KAHNISTIOH,
      KITHANINK => KAHNISTIOH_KITHANINK,
    ];
  }

  public function getDefaultControl()
  {
    $control = Globals::getControlIroquois();
    if ($control === NEUTRAL) {
      return NEUTRAL;
    } else {
      return $control;
    }
  }
}
