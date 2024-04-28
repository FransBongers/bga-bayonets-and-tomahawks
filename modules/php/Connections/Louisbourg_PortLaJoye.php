<?php

namespace BayonetsAndTomahawks\Connections;

class Louisbourg_PortLaJoye extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG_PORT_LA_JOYE;
    $this->coastal = true;
  }
}
