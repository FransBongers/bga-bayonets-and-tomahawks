<?php

namespace BayonetsAndTomahawks\Connections;

class Kingston_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KINGSTON_OQUAGA;
    $this->indianPath = true;
    $this->top = 1543;
    $this->left = 800;
  }
}
