<?php

namespace BayonetsAndTomahawks\Connections;

class Mekekasink_WillsCreek extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MEKEKASINK_WILLS_CREEK;
  }
}
