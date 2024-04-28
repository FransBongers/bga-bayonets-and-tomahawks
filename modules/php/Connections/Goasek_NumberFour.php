<?php

namespace BayonetsAndTomahawks\Connections;

class Goasek_NumberFour extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK_NUMBER_FOUR;
  }
}