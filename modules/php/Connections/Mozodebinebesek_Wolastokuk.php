<?php

namespace BayonetsAndTomahawks\Connections;

class Mozodebinebesek_Wolastokuk extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOZODEBINEBESEK_WOLASTOKUK;
    $this->top = 738;
    $this->left = 618;
  }
}
