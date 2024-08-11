<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_RaysTown extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_RAYS_TOWN;
    $this->top = 1932;
    $this->left = 913;
  }
}