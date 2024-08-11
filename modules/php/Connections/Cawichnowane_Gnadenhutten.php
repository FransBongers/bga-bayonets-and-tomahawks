<?php

namespace BayonetsAndTomahawks\Connections;

class Cawichnowane_Gnadenhutten extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE_GNADENHUTTEN;
    $this->top = 1747;
    $this->left = 696;
  }
}