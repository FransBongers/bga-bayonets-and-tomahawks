<?php

namespace BayonetsAndTomahawks\Connections;

class Minisink_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MINISINK_OQUAGA;
    $this->indianNationPath = IROQUOIS;
    $this->top = 1643;
    $this->left = 790;
  }
}
