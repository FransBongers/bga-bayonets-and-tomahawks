<?php

namespace BayonetsAndTomahawks\Connections;

class Assunepachla_Cawichnowane extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA_CAWICHNOWANE;
    $this->top = 1832;
    $this->left = 663;
  }
}
