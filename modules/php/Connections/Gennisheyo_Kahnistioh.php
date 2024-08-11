<?php

namespace BayonetsAndTomahawks\Connections;

class Gennisheyo_Kahnistioh extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GENNISHEYO_KAHNISTIOH;
    $this->indianPath = true;
    $this->top = 1714;
    $this->left = 491;
  }
}