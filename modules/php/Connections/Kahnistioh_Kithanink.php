<?php

namespace BayonetsAndTomahawks\Connections;

class Kahnistioh_Kithanink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHNISTIOH_KITHANINK;
    $this->indianPath = true;
  }
}
