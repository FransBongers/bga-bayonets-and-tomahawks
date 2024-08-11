<?php

namespace BayonetsAndTomahawks\ActionPoints;

class IndianAP extends \BayonetsAndTomahawks\Models\ActionPoint
{
  public function __construct()
  {
    parent::__construct();
    $this->id = INDIAN_AP;
    $this->name = clienttranslate("Indian AP");
    $this->actionsAllowed = [
      MOVEMENT,
      RAID
    ];
  }
}
