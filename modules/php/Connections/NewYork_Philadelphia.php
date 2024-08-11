<?php

namespace BayonetsAndTomahawks\Connections;

class NewYork_Philadelphia extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_YORK_PHILADELPHIA;
    $this->coastal = true;
    $this->top = 1688;
    $this->left = 1125;
  }
}
