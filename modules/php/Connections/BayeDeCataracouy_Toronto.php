<?php

namespace BayonetsAndTomahawks\Connections;

class BayeDeCataracouy_Toronto extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BAYE_DE_CATARACOUY_TORONTO;
    $this->top = 1536;
    $this->left = 304;
  }
}
