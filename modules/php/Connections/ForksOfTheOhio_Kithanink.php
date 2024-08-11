<?php

namespace BayonetsAndTomahawks\Connections;

class ForksOfTheOhio_Kithanink extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO_KITHANINK;
    $this->top = 1961;
    $this->left = 620;
  }
}