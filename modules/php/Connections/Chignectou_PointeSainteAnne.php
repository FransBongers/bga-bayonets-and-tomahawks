<?php

namespace BayonetsAndTomahawks\Connections;

class Chignectou_PointeSainteAnne extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU_POINTE_SAINTE_ANNE;
    $this->top = 607;
    $this->left = 838;
  }
}