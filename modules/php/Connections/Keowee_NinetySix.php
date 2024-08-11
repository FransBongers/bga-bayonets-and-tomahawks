<?php

namespace BayonetsAndTomahawks\Connections;

class Keowee_NinetySix extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KEOWEE_NINETY_SIX;
    $this->indianPath = true;
    $this->top = 2195;
    $this->left = 1113;
  }
}
