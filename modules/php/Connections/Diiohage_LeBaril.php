<?php

namespace BayonetsAndTomahawks\Connections;

class Diiohage_LeBaril extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE_LE_BARIL;
    $this->top = 2037;
    $this->left = 459;
  }
}