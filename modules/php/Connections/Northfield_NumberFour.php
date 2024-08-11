<?php

namespace BayonetsAndTomahawks\Connections;

class Northfield_NumberFour extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NORTHFIELD_NUMBER_FOUR;
    $this->top = 1183;
    $this->left = 828;
  }
}
