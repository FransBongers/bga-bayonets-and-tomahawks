<?php

namespace BayonetsAndTomahawks\Connections;

class LakeGeorge_Ticonderoga extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LAKE_GEORGE_TICONDEROGA;
    $this->top = 1263;
    $this->left = 588;
  }
}
