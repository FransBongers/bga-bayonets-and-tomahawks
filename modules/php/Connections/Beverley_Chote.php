<?php

namespace BayonetsAndTomahawks\Connections;

class Beverley_Chote extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BEVERLEY_CHOTE;
    $this->indianPath = true;
    $this->top = 2141;
    $this->left = 1006;
  }
}
