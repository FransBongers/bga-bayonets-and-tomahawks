<?php

namespace BayonetsAndTomahawks\Connections;

class Cawichnowane_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE_OQUAGA;
    $this->indianNationPath = IROQUOIS;
    $this->top = 1708;
    $this->left = 656;
  }
}