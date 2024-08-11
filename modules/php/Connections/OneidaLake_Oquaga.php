<?php

namespace BayonetsAndTomahawks\Connections;

class OneidaLake_Oquaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONEIDA_LAKE_OQUAGA;
    $this->indianPath = true;
    $this->top = 1543;
    $this->left = 656;
  }
}
