<?php

namespace BayonetsAndTomahawks\Connections;

class Assunepachla_Loyalhanna extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA_LOYALHANNA;
    $this->top = 1935;
    $this->left = 711;
  }
}
