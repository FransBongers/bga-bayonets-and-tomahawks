<?php

namespace BayonetsAndTomahawks\Connections;

class Albany_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY_OQUAGA;
    $this->indianPath = true;
  }
}
