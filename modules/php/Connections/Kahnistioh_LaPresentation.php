<?php

namespace BayonetsAndTomahawks\Connections;

class Kahnistioh_LaPresentation extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHUAHGO_LA_PRESENTATION;
  }
}
