<?php

namespace BayonetsAndTomahawks\Connections;

class Boston_Northfield extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BOSTON_NORTHFIELD;
  }
}
