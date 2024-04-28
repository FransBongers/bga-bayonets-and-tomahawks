<?php

namespace BayonetsAndTomahawks\Connections;

class Chote_Keninsheka extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHOTE_KENINSHEKA;
    $this->indianPath = true;
  }
}