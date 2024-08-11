<?php

namespace BayonetsAndTomahawks\Connections;

class Onontake_Oswego extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONONTAKE_OSWEGO;
    $this->indianPath = true;
    $this->top = 1550;
    $this->left = 424;
  }
}
