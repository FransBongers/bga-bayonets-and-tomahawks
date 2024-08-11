<?php

namespace BayonetsAndTomahawks\Connections;

class NewLondon_NewYork extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_LONDON_NEW_YORK;
    $this->coastal = true;
    $this->top = 1493;
    $this->left = 1080;
  }
}
