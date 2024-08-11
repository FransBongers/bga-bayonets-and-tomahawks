<?php

namespace BayonetsAndTomahawks\Connections;

class Chignectou_Halifax extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU_HALIFAX;
    $this->top = 504;
    $this->left = 1003;
  }
}