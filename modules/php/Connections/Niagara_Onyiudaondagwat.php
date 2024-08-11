<?php

namespace BayonetsAndTomahawks\Connections;

class Niagara_Onyiudaondagwat extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIAGARA_ONYIUDAONDAGWAT;
    $this->top = 1629;
    $this->left = 374;
  }
}
