<?php

namespace BayonetsAndTomahawks\ActionPoints;

class LightAP_2X extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = LIGHT_AP_2X;
    $this->movementMultiplier = 2;
    $this->name = clienttranslate("Light AP 2x");
    $this->actionsAllowed = [
      MOVEMENT,
      RAID
    ];
  }
}
