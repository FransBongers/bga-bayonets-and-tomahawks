<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_Easton extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_EASTON;
    $this->top = 1817;
    $this->left = 919;
  }
}