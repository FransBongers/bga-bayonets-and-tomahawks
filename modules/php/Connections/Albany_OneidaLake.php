<?php

namespace BayonetsAndTomahawks\Connections;

class Albany_OneidaLake extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY_ONEIDA_LAKE;
    $this->top = 1465;
    $this->left = 658;
  }
}
