<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Globals;

class Onontake extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONONTAKE;
    $this->battlePriority = 183;
    $this->defaultControl = INDIAN;
    $this->indianVillage = IROQUOIS;
    $this->name = clienttranslate('Onontake');
    $this->victorySpace = false;
    $this->top = 1632.5;
    $this->left = 580;
    $this->adjacentSpaces = [
      GENNISHEYO => GENNISHEYO_ONONTAKE,
      OQUAGA => ONONTAKE_OQUAGA, 
      OSWEGO => ONONTAKE_OSWEGO,
    ];
  }

  public function getDefaultControl()
  {
    $control = Globals::getControlIroquois();
    if ($control === NEUTRAL) {
      return INDIAN;
    } else {
      return $control;
    }
  }
}
