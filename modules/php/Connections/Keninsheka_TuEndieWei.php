<?php

namespace BayonetsAndTomahawks\Connections;

class Keninsheka_TuEndieWei extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KENINSHEKA_TU_ENDIE_WEI;
    $this->top = 2141;
    $this->left = 660;
  }
}
