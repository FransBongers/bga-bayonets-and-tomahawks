<?php

namespace BayonetsAndTomahawks\Connections;

class Gnadenhutten_Shamokin extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN_SHAMOKIN;
    $this->top = 1754;
    $this->left = 779;
  }
}