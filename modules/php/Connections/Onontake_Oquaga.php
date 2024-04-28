<?php

namespace BayonetsAndTomahawks\Connections;

class Onontake_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONONTAKE_OQUAGA;
    $this->indianPath = true;
  }
}
