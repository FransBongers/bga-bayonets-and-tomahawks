<?php

namespace BayonetsAndTomahawks\Connections;

class Gennisheyo_Onyiudaondagwat extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GENNISHEYO_ONYIUDAONDAGWAT;
  }
}