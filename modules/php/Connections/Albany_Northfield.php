<?php

namespace BayonetsAndTomahawks\Connections;

class Albany_Northfield extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY_NORTHFIELD;
    $this->top = 1327;
    $this->left = 853;
  }
}
