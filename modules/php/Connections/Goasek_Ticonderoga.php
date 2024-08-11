<?php

namespace BayonetsAndTomahawks\Connections;

class Goasek_Ticonderoga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK_TICONDEROGA;
    $this->top = 1100;
    $this->left = 528;
  }
}