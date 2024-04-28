<?php

namespace BayonetsAndTomahawks\Connections;

class LaPresentation_Montreal extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESENTATION_MONTREAL;
  }
}
