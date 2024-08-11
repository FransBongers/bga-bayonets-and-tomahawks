<?php

namespace BayonetsAndTomahawks\Connections;

class Chignectou_Miramichy extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU_MIRAMICHY;
    $this->top = 522;
    $this->left = 779;
  }
}