<?php

namespace BayonetsAndTomahawks\Connections;

class Rumford_Zawakwtegok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RUMFORD_ZAWAKWTEGOK;
  }
}
