<?php

namespace BayonetsAndTomahawks\Connections;

class Gnadenhutten_Minisink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN_MINISINK;
    $this->top = 1667;
    $this->left = 838;
  }
}