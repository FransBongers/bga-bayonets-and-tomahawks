<?php

namespace BayonetsAndTomahawks\Connections;

class OneidaLake_Oswego extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONEIDA_LAKE_OSWEGO;
  }
}
