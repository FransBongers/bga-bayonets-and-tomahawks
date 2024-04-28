<?php

namespace BayonetsAndTomahawks\Connections;

class LaPresquIsle_Niagara extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESQU_ISLE_NIAGARA;
  }
}
