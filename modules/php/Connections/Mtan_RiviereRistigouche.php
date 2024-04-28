<?php

namespace BayonetsAndTomahawks\Connections;

class Mtan_RiviereRistigouche extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MTAN_RIVIERE_RISTIGOUCHE;
  }
}
