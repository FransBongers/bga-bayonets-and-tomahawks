<?php

namespace BayonetsAndTomahawks\Connections;

class LeBaril_RiviereOuabache extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_BARIL_RIVIERE_OUABACHE;
    $this->top = 2163;
    $this->left = 390;
  }
}
