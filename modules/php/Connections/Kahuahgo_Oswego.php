<?php

namespace BayonetsAndTomahawks\Connections;

class Kahuahgo_Oswego extends \BayonetsAndTomahawks\Models\Connections\Path
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHUAHGO_OSWEGO;
  }
}
