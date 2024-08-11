<?php

namespace BayonetsAndTomahawks\Connections;

class NewLondon_Northfield extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_LONDON_NORTHFIELD;
    $this->top = 1341;
    $this->left = 957;
  }
}
