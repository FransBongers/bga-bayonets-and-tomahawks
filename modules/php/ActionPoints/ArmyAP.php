<?php

namespace BayonetsAndTomahawks\ActionPoints;

class ArmyAP extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = ARMY_AP;
    $this->name = clienttranslate("Army AP");
    $this->actionsAllowed = [
      MOVEMENT,
      // CONSTRUCTION,
      // MARSHAL_TROOPS,
    ];
  }
}
