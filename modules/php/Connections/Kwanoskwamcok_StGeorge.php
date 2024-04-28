<?php

namespace BayonetsAndTomahawks\Connections;

class Kwanoskwamcok_StGeorge extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KWANOSKWAMCOK_ST_GEORGE;
    $this->coastal = true;
  }
}
