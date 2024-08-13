<?php

namespace BayonetsAndTomahawks\ActionPoints;

class SailArmyAP_2x extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = SAIL_ARMY_AP;
    $this->movementMultiplier = 2;
    $this->name = clienttranslate("Sail/Army AP 2x");
    $this->actionsAllowed = [
      MOVEMENT,
      // SAIL_MOVEMENT,
      MARSHAL_TROOPS,
      CONSTRUCTION,
    ];
  }
}
