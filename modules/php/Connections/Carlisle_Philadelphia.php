<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_Philadelphia extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_PHILADELPHIA;
  }
}