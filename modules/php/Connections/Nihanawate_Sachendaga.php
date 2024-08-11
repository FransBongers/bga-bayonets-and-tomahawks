<?php

namespace BayonetsAndTomahawks\Connections;

class Nihanawate_Sachendaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIHANAWATE_SACHENDAGA;
    $this->top = 1318;
    $this->left = 468;
  }
}
