<?php

namespace BayonetsAndTomahawks\ActionPoints;

class ArmyAP_2x extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = ARMY_AP;
    $this->movementMultiplier = 2;
    $this->name = clienttranslate("Army AP 2x");
    $this->actionsAllowed = [
      MOVEMENT,
      // CONSTRUCTION,
      // MARSHAL_TROOPS,
    ];
  }
}
