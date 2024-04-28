<?php

namespace BayonetsAndTomahawks\Connections;

class Boston_York extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BOSTON_YORK;
    $this->coastal = true;
  }
}
