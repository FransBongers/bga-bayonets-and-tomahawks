<?php

namespace BayonetsAndTomahawks\Connections;

class LakeGeorge_Sachendaga extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LAKE_GEORGE_SACHENDAGA;
  }
}
