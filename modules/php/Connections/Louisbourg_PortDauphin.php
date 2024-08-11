<?php

namespace BayonetsAndTomahawks\Connections;

class Louisbourg_PortDauphin extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG_PORT_DAUPHIN;
    $this->coastal = true;
    $this->top = 241;
    $this->left = 1026;
  }
}
