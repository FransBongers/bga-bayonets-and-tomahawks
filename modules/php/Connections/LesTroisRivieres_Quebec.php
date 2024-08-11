<?php

namespace BayonetsAndTomahawks\Connections;

class LesTroisRivieres_Quebec extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_TROIS_RIVIERES_QUEBEC;
    $this->top = 910;
    $this->left = 371;
  }
}
