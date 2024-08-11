<?php

namespace BayonetsAndTomahawks\Connections;

class GrandSault_Matawaskiyak extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GRAND_SAULT_MATAWASKIYAK;
    $this->top = 599;
    $this->left = 530;
  }
}
