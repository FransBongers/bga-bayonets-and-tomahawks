<?php

namespace BayonetsAndTomahawks\Connections;

class Ouentironk_Toronto extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OUENTIRONK_TORONTO;
  }
}
