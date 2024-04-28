<?php

namespace BayonetsAndTomahawks\Connections;

class LesTroisRivieres_Montreal extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_TROIS_RIVIERES_MONTREAL;
  }
}
