<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDeBeaupre_Quebec extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE_QUEBEC;
    $this->coastal = true;
  }
}