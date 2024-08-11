<?php

namespace BayonetsAndTomahawks\Connections;

class Easton_Minisink extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = EASTON_MINISINK;
    $this->top = 1693;
    $this->left = 912;
  }
}