<?php

namespace BayonetsAndTomahawks\Connections;

class Chignectou_PortLaJoye extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU_PORT_LA_JOYE;
  }
}