<?php

namespace BayonetsAndTomahawks\Connections;

class Nihanawate_Saranac extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIHANAWATE_SARANAC;
    $this->top = 1257;
    $this->left = 400;
  }
}
