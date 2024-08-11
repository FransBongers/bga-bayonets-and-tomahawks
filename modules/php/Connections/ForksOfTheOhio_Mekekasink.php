<?php

namespace BayonetsAndTomahawks\Connections;

class ForksOfTheOhio_Mekekasink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO_MEKEKASINK;
    $this->top = 2068;
    $this->left = 710;
  }
}