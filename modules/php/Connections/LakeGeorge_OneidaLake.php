<?php

namespace BayonetsAndTomahawks\Connections;

class LakeGeorge_OneidaLake extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LAKE_GEORGE_ONEIDA_LAKE;
  }
}
