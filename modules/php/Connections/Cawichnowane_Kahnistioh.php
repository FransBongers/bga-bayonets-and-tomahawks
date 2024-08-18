<?php

namespace BayonetsAndTomahawks\Connections;

class Cawichnowane_Kahnistioh extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE_KAHNISTIOH;
    $this->indianNationPath = IROQUOIS;
    $this->top = 1790;
    $this->left = 554;
  }
}