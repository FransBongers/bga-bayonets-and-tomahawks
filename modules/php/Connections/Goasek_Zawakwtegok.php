<?php

namespace BayonetsAndTomahawks\Connections;

class Goasek_Zawakwtegok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK_ZAWAKWTEGOK;
  }
}