<?php

namespace BayonetsAndTomahawks\Connections;

class WillsCreek_Winchester extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WILLS_CREEK_WINCHESTER;
    $this->top = 1986;
    $this->left = 923;
  }
}
