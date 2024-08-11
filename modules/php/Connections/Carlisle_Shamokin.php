<?php

namespace BayonetsAndTomahawks\Connections;

class Carlisle_Shamokin extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE_SHAMOKIN;
    $this->top = 1861;
    $this->left = 868;
  }
}