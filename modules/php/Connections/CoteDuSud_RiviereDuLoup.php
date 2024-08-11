<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDuSud_RiviereDuLoup extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DU_SUD_RIVIERE_DU_LOUP;
    $this->coastal = true;
    $this->top = 661;
    $this->left = 399;
  }
}