<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDeBeaupre_CoteDuSud extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE_COTE_DU_SUD;
    $this->coastal = true;
    $this->top = 714;
    $this->left = 388;
  }
}