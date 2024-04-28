<?php

namespace BayonetsAndTomahawks\Connections;

class Molojoak_Mozodebinebesek extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOLOJOAK_MOZODEBINEBESEK;
  }
}
