<?php

namespace BayonetsAndTomahawks\Connections;

class LeBaril_TuEndieWei extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_BARIL_TU_ENDIE_WEI;
  }
}
