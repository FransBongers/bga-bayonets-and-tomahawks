<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_Winchester extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_WINCHESTER;
    $this->top = 1936;
    $this->left = 960;
  }
}