<?php

namespace BayonetsAndTomahawks\Connections;

class Albany_Kingston extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY_KINGSTON;
    $this->top = 1493;
    $this->left = 815;
  }
}
