<?php

namespace BayonetsAndTomahawks\ActionPoints;

class FrenchLightArmyAP extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = FRENCH_LIGHT_ARMY_AP;
    $this->name = clienttranslate("French Light/Army AP");
    $this->actionsAllowed = [
      RAID_SELECT_TARGET,
      MOVEMENT,
      MARSHAL_TROOPS,
      CONSTRUCTION,
    ];
  }
}
