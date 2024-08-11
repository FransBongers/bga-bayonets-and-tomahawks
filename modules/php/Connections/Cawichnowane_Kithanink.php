<?php

namespace BayonetsAndTomahawks\Connections;

class Cawichnowane_Kithanink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE_KITHANINK;
    $this->top = 1832;
    $this->left = 597;
  }
}