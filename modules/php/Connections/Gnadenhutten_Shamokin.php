<?php

namespace BayonetsAndTomahawks\Connections;

class Gnadenhutten_Shamokin extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN_SHAMOKIN;
  }
}