<?php

namespace BayonetsAndTomahawks\Connections;

class StGeorge_Taconnet extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ST_GEORGE_TACONNET;
    $this->top = 830;
    $this->left = 826;
  }
}
