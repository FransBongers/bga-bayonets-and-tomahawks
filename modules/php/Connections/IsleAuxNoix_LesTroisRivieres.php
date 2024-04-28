<?php

namespace BayonetsAndTomahawks\Connections;

class IsleAuxNoix_LesTroisRivieres extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX_LES_TROIS_RIVIERES;
  }
}
