<?php

namespace BayonetsAndTomahawks\Connections;

class AnnapolisRoyal_Chignectou extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ANNAPOLIS_ROYAL_CHIGNECTOU;
    $this->coastal = true;
    $this->top = 544;
    $this->left = 972;
  }
}
