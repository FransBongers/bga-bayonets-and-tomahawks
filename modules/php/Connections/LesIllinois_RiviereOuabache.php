<?php

namespace BayonetsAndTomahawks\Connections;

class LesIllinois_RiviereOuabache extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_ILLINOIS_RIVIERE_OUABACHE;
    $this->top = 2243;
    $this->left = 475;
  }
}
