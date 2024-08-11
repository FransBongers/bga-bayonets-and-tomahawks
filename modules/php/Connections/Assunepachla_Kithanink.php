<?php

namespace BayonetsAndTomahawks\Connections;

class Assunepachla_Kithanink extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA_KITHANINK;
    $this->top = 1883;
    $this->left = 635;
  }
}
