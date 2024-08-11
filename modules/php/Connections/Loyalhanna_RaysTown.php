<?php

namespace BayonetsAndTomahawks\Connections;

class Loyalhanna_RaysTown extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOYALHANNA_RAYS_TOWN;
    $this->top = 1957;
    $this->left = 760;
  }
}
