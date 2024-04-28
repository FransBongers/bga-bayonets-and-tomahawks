<?php

namespace BayonetsAndTomahawks\Connections;

class Beverley_Winchester extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BEVERLEY_WINCHESTER;
  }
}
