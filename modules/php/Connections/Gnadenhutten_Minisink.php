<?php

namespace BayonetsAndTomahawks\Connections;

class Gnadenhutten_Minisink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN_MINISINK;
  }
}