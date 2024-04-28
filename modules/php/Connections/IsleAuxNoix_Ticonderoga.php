<?php

namespace BayonetsAndTomahawks\Connections;

class IsleAuxNoix_Ticonderoga extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX_TICONDEROGA;
  }
}
