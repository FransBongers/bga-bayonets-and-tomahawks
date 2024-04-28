<?php

namespace BayonetsAndTomahawks\Connections;

class Molojoak_Zawakwtegok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOLOJOAK_ZAWAKWTEGOK;
  }
}
