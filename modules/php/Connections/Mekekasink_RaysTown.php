<?php

namespace BayonetsAndTomahawks\Connections;

class Mekekasink_RaysTown extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MEKEKASINK_RAYS_TOWN;
    $this->top = 2005;
    $this->left = 804;
  }
}
