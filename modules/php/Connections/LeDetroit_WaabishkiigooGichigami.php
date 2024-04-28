<?php

namespace BayonetsAndTomahawks\Connections;

class LeDetroit_WaabishkiigooGichigami extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_DETROIT_WAABISHKIIGOO_GICHIGAMI;
  }
}
