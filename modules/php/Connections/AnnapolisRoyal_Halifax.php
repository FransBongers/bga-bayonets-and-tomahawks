<?php

namespace BayonetsAndTomahawks\Connections;

class AnnapolisRoyal_Halifax extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ANNAPOLIS_ROYAL_HALIFAX;
  }
}
