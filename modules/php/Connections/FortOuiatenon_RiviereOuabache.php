<?php

namespace BayonetsAndTomahawks\Connections;

class FortOuiatenon_RiviereOuabache extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORT_OUIATENON_RIVIERE_OUABACHE;
  }
}