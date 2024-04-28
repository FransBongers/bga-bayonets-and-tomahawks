<?php

namespace BayonetsAndTomahawks\Connections;

class Alexandria_Winchester extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALEXANDRIA_WINCHESTER;
  }
}
