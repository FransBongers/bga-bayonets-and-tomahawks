<?php

namespace BayonetsAndTomahawks\Connections;

class Rumford_York extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RUMFORD_YORK;
  }
}
