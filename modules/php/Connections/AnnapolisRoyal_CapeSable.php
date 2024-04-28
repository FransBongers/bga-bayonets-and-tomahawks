<?php

namespace BayonetsAndTomahawks\Connections;

class AnnapolisRoyal_CapeSable extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ANNAPOLIS_ROYAL_CAPE_SABLE;
    $this->coastal = true;
  }
}
