<?php

namespace BayonetsAndTomahawks\Connections;

class Diiohage_LeDetroit extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE_LE_DETROIT;
  }
}