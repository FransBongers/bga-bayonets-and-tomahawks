<?php

namespace BayonetsAndTomahawks\Connections;

class IsleAuxNoix_Saranac extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX_SARANAC;
    $this->top = 1177;
    $this->left = 384;
  }
}
