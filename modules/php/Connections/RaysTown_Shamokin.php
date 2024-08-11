<?php

namespace BayonetsAndTomahawks\Connections;

class RaysTown_Shamokin extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RAYS_TOWN_SHAMOKIN;
    $this->top = 1884;
    $this->left = 792;
  }
}
