<?php

namespace BayonetsAndTomahawks\Connections;

class Taconnet_York extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TACONNET_YORK;
    $this->top = 899;
    $this->left = 830;
  }
}
