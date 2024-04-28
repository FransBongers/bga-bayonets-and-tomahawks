<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDuSud_Wolastokuk extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DU_SUD_WOLASTOKUK;
  }
}