<?php

namespace BayonetsAndTomahawks\Connections;

class JacquesCartier_LesTroisRivieres extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = JACQUES_CARTIER_LES_TROIS_RIVIERES;
    $this->top = 911;
    $this->left = 326;
  }
}
