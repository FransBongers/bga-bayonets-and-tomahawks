<?php

namespace BayonetsAndTomahawks\Connections;

class Miramichy_RiviereRistigouche extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIRAMICHY_RIVIERE_RISTIGOUCHE;
    $this->coastal = true;
    $this->top = 450;
    $this->left = 616;
  }
}
