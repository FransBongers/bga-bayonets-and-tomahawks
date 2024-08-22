<?php

namespace BayonetsAndTomahawks\Connections;

class Onyiudaondagwat_Oswego extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONYIUDAONDAGWAT_OSWEGO;
    $this->top = 1550;
    $this->left = 424;
  }
}
