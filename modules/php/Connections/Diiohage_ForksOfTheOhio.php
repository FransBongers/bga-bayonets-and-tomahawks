<?php

namespace BayonetsAndTomahawks\Connections;

class Diiohage_ForksOfTheOhio extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE_FORKS_OF_THE_OHIO;
    $this->top = 2008;
    $this->left = 541;
  }
}