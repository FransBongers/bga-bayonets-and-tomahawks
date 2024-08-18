<?php

namespace BayonetsAndTomahawks\Connections;

class Chote_Keowee extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHOTE_KEOWEE;
    $this->indianNationPath = CHEROKEE;
    $this->top = 2228;
    $this->left = 990;
  }
}