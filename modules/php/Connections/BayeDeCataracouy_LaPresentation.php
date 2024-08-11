<?php

namespace BayonetsAndTomahawks\Connections;

class BayeDeCataracouy_LaPresentation extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BAYE_DE_CATARACOUY_LA_PRESENTATION;
    $this->top = 1402;
    $this->left = 297;
  }
}
