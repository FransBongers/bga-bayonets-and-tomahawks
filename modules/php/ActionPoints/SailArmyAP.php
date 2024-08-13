<?php

namespace BayonetsAndTomahawks\ActionPoints;

class SailArmyAP extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = SAIL_ARMY_AP;
    $this->name = clienttranslate("Sail/Army AP");
    $this->actionsAllowed = [
      MOVEMENT,
      // SAIL_MOVEMENT,
      MARSHAL_TROOPS,
      // CONSTRUCTION,
    ];
  }
}
