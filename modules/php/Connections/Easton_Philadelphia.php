<?php

namespace BayonetsAndTomahawks\Connections;

class Easton_Philadelphia extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = EASTON_PHILADELPHIA;
    $this->top = 1791;
    $this->left = 975;
  }
}