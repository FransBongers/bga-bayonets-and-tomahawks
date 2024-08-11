<?php

namespace BayonetsAndTomahawks\Connections;

class Kingston_Minisink extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KINGSTON_MINISINK;
    $this->top = 1563;
    $this->left = 873;
  }
}
