<?php

namespace BayonetsAndTomahawks\Connections;

class Matawaskiyak_RiviereDuLoup extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATAWASKIYAK_RIVIERE_DU_LOUP;
    $this->top = 583;
    $this->left = 436;
  }
}
