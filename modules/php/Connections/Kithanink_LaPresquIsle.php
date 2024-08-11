<?php

namespace BayonetsAndTomahawks\Connections;

class Kithanink_LaPresquIsle extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KITHANINK_LA_PRESQU_ISLE;
    $this->top = 1865;
    $this->left = 495;
  }
}
