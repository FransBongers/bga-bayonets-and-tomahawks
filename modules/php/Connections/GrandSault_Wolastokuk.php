<?php

namespace BayonetsAndTomahawks\Connections;

class GrandSault_Wolastokuk extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GRAND_SAULT_WOLASTOKUK;
    $this->top = 670;
    $this->left = 598;
  }
}
