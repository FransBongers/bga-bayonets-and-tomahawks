<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDuSud_Quebec extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DU_SUD_QUEBEC;
    $this->coastal = true;
  }
}