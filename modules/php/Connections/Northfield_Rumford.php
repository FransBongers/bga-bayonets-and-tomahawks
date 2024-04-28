<?php

namespace BayonetsAndTomahawks\Connections;

class Northfield_Rumford extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NORTHFIELD_RUMFORD;
  }
}
