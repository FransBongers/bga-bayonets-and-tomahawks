<?php

namespace BayonetsAndTomahawks\Connections;

class Kahuahgo_Nihanawate extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHUAHGO_NIHANAWATE;
    $this->top = 1363;
    $this->left = 441;
  }
}
