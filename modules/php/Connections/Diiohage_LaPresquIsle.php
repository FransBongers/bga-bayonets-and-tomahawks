<?php

namespace BayonetsAndTomahawks\Connections;

class Diiohage_LaPresquIsle extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE_LA_PRESQU_ISLE;
  }
}