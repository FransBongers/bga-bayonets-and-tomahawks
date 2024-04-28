<?php

namespace BayonetsAndTomahawks\Connections;

class Matschedash_Ouentironk extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATSCHEDASH_OUENTIRONK;
  }
}
