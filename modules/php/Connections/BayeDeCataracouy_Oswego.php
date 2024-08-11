<?php

namespace BayonetsAndTomahawks\Connections;

class BayeDeCataracouy_Oswego extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BAYE_DE_CATARACOUY_OSWEGO;
    $this->top = 1463;
    $this->left = 364;
  }
}
