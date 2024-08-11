<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_Philadelphia extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_PHILADELPHIA;
    $this->top = 1847;
    $this->left = 999;
  }
}