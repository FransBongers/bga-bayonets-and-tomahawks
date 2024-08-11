<?php

namespace BayonetsAndTomahawks\Connections;

class Diiohage_LeDetroit extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE_LE_DETROIT;
    $this->top = 2072;
    $this->left = 380;
  }
}