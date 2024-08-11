<?php

namespace BayonetsAndTomahawks\Connections;

class Goasek_Mamhlawbagok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK_MAMHLAWBAGOK;
    $this->top = 1020;
    $this->left = 570;
  }
}