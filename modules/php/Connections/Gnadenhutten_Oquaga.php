<?php

namespace BayonetsAndTomahawks\Connections;

class Gnadenhutten_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GNADENHUTTEN_OQUAGA;
    $this->indianNationPath = IROQUOIS;
    $this->top = 1682;
    $this->left = 718;
  }
}