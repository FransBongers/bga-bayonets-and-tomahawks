<?php

namespace BayonetsAndTomahawks\Connections;

class Chignectou_Kwanoskwamcok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU_KWANOSKWAMCOK;
    $this->coastal = true;
  }
}