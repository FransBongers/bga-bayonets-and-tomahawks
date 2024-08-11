<?php

namespace BayonetsAndTomahawks\Connections;

class Namaskonkik_Quebec extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NAMASKONKIK_QUEBEC;
    $this->top = 812;
    $this->left = 514;
  }
}
