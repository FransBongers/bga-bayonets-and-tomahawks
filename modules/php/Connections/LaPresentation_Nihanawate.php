<?php

namespace BayonetsAndTomahawks\Connections;

class LaPresentation_Nihanawate extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESENTATION_NIHANAWATE;
    $this->top = 1314;
    $this->left = 348;
  }
}
