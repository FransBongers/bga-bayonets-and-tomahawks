<?php

namespace BayonetsAndTomahawks\Connections;

class Saranac_Ticonderoga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SARANAC_TICONDEROGA;
    $this->top = 1212;
    $this->left = 455;
  }
}
