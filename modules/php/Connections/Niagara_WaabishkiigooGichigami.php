<?php

namespace BayonetsAndTomahawks\Connections;

class Niagara_WaabishkiigooGichigami extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIAGARA_WAABISHKIIGOO_GICHIGAMI;
  }
}
