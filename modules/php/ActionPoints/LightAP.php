<?php

namespace BayonetsAndTomahawks\ActionPoints;

class LightAP extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = LIGHT_AP;
    $this->name = clienttranslate("Light AP");
    $this->actionsAllowed = [
      MOVEMENT,
      RAID
    ];
  }
}
