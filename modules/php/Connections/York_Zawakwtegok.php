<?php

namespace BayonetsAndTomahawks\Connections;

class York_Zawakwtegok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = YORK_ZAWAKWTEGOK;
    $this->top = 988;
    $this->left = 833;
  }
}
