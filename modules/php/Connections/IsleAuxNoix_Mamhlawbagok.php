<?php

namespace BayonetsAndTomahawks\Connections;

class IsleAuxNoix_Mamhlawbagok extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ISLE_AUX_NOIX_MAMHLAWBAGOK;
    $this->top = 1038;
    $this->left = 480;
  }
}
