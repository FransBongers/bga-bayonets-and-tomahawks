<?php

namespace BayonetsAndTomahawks\Connections;

class Easton_Gnadenhutten extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = EASTON_GNADENHUTTEN;
  }
}