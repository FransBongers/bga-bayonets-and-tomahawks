<?php

namespace BayonetsAndTomahawks\Connections;

class Onontake_Oswego extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONONTAKE_OSWEGO;
    $this->indianNationPath = IROQUOIS;
    $this->top = 1577;
    $this->left = 524;
  }
}
