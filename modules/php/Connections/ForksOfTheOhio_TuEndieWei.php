<?php

namespace BayonetsAndTomahawks\Connections;

class ForksOfTheOhio_TuEndieWei extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO_TU_ENDIE_WEI;
  }
}