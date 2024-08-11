<?php

namespace BayonetsAndTomahawks\Connections;

class Kadesquit_PointeSainteAnne extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KADESQUIT_POINTE_SAINTE_ANNE;
    $this->top = 685;
    $this->left = 785;
  }
}
