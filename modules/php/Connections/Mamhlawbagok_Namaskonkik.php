<?php

namespace BayonetsAndTomahawks\Connections;

class Mamhlawbagok_Namaskonkik extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MAMHLAWBAGOK_NAMASKONKIK;
  }
}
