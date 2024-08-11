<?php

namespace BayonetsAndTomahawks\Connections;

class CharlesTown_NinetySix extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHARLES_TOWN_NINETY_SIX;
    $this->top = 2249;
    $this->left = 1279;
  }
}