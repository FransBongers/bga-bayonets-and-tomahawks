<?php

namespace BayonetsAndTomahawks\Connections;

class NumberFour_Ticonderoga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NUMBER_FOUR_TICONDEROGA;
  }
}
