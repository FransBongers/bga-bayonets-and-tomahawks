<?php

namespace BayonetsAndTomahawks\Connections;

class Assunepachla_RaysTown extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA_RAYS_TOWN;
    $this->top = 1917;
    $this->left = 761;
  }
}
