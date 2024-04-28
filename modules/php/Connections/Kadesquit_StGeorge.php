<?php

namespace BayonetsAndTomahawks\Connections;

class Kadesquit_StGeorge extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KADESQUIT_ST_GEORGE;
  }
}
