<?php

namespace BayonetsAndTomahawks\Connections;

class Mtan_RiviereDuLoup extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MTAN_RIVIERE_DU_LOUP;
    $this->coastal = true;
    $this->top = 510;
    $this->left = 359;
  }
}
