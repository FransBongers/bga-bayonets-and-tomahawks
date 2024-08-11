<?php

namespace BayonetsAndTomahawks\Connections;

class Niagara_Toronto extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIAGARA_TORONTO;
    $this->top = 1742;
    $this->left = 282;
  }
}
