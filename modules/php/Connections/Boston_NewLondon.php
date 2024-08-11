<?php

namespace BayonetsAndTomahawks\Connections;

class Boston_NewLondon extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BOSTON_NEW_LONDON;
    $this->coastal = true;
    $this->top = 1219;
    $this->left = 1115;
  }
}
