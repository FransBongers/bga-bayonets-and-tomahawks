<?php

namespace BayonetsAndTomahawks\Connections;

class JacquesCartier_Quebec extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = JACQUES_CARTIER_QUEBEC;
  }
}
