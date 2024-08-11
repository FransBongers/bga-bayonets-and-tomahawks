<?php

namespace BayonetsAndTomahawks\Connections;

class Mikazawitegok_NumberFour extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIKAZAWITEGOK_NUMBER_FOUR;
    $this->top = 1189;
    $this->left = 746;
  }
}
