<?php

namespace BayonetsAndTomahawks\Connections;

class CapeSable_Halifax extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAPE_SABLE_HALIFAX;
    $this->coastal = true;
    $this->top = 649;
    $this->left = 1081;
  }
}
