<?php

namespace BayonetsAndTomahawks\Connections;

class Albany_LakeGeorge extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY_LAKE_GEORGE;
  }
}
