<?php

namespace BayonetsAndTomahawks\Cards;

class Card44 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card44';
    $this->faction = INDIAN;
  }
}
