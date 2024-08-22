<?php

namespace BayonetsAndTomahawks\ActionPoints;

class IndianAP_2x extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = INDIAN_AP_2X;
    $this->movementMultiplier = 2;
    $this->name = clienttranslate("Indian AP 2x");
    $this->actionsAllowed = [
      MOVEMENT,
      RAID_SELECT_TARGET
    ];
  }
}
