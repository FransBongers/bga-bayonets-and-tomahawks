<?php

namespace BayonetsAndTomahawks\Connections;

class Taconnet_York extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TACONNET_YORK;
  }
}
