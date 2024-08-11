<?php

namespace BayonetsAndTomahawks\Connections;

class StGeorge_York extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ST_GEORGE_YORK;
    $this->coastal = true;
    $this->top = 899;
    $this->left = 908;
  }
}
