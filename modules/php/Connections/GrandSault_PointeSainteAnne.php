<?php

namespace BayonetsAndTomahawks\Connections;

class GrandSault_PointeSainteAnne extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GRAND_SAULT_POINTE_SAINTE_ANNE;
  }
}
