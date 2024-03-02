<?php

namespace BayonetsAndTomahawks\Cards;

class Card46 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card46';
    $this->faction = INDIAN;
  }
}
