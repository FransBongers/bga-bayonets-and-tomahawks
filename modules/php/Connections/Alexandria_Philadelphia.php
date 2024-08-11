<?php

namespace BayonetsAndTomahawks\Connections;

class Alexandria_Philadelphia extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALEXANDRIA_PHILADELPHIA;
    $this->coastal = true;
    $this->top = 1926;
    $this->left = 1085;
  }
}
