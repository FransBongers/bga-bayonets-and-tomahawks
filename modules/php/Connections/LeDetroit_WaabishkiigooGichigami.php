<?php

namespace BayonetsAndTomahawks\Connections;

class LeDetroit_WaabishkiigooGichigami extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_DETROIT_WAABISHKIIGOO_GICHIGAMI;
    $this->top = 2017;
    $this->left = 276;
  }
}
