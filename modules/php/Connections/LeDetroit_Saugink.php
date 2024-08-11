<?php

namespace BayonetsAndTomahawks\Connections;

class LeDetroit_Saugink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_DETROIT_SAUGINK;
    $this->top = 1977;
    $this->left = 65;
  }
}
