<?php

namespace BayonetsAndTomahawks\Cards;

class Card49 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card49';
    $this->faction = INDIAN;
  }
}
