<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDeBeaupre_JacquesCartier extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE_JACQUES_CARTIER;
    $this->top = 800;
    $this->left = 226;
  }
}