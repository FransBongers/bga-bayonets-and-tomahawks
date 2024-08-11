<?php

namespace BayonetsAndTomahawks\Connections;

class Cawichnowane_Shamokin extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE_SHAMOKIN;
    $this->top = 1822;
    $this->left = 713;
  }
}