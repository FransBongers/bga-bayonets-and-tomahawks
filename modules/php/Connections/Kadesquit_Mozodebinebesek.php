<?php

namespace BayonetsAndTomahawks\Connections;

class Kadesquit_Mozodebinebesek extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KADESQUIT_MOZODEBINEBESEK;
    $this->top = 705;
    $this->left = 718;
  }
}
