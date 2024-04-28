<?php

namespace BayonetsAndTomahawks\Connections;

class LesTroisRivieres_Mamhlawbagok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_TROIS_RIVIERES_MAMHLAWBAGOK;
  }
}
