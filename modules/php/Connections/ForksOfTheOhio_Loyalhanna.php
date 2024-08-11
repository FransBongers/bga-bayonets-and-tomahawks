<?php

namespace BayonetsAndTomahawks\Connections;

class ForksOfTheOhio_Loyalhanna extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO_LOYALHANNA;
    $this->top = 2011;
    $this->left = 710;
  }
}