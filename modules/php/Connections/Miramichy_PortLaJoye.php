<?php

namespace BayonetsAndTomahawks\Connections;

class Miramichy_PortLaJoye extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIRAMICHY_PORT_LA_JOYE;
    $this->coastal = true;
    $this->top = 476;
    $this->left = 795;
  }
}
