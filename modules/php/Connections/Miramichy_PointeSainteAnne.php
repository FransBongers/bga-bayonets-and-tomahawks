<?php

namespace BayonetsAndTomahawks\Connections;

class Miramichy_PointeSainteAnne extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIRAMICHY_POINTE_SAINTE_ANNE;
    $this->top = 574;
    $this->left = 707;
  }
}
