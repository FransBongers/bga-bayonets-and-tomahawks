<?php

namespace BayonetsAndTomahawks\Cards;

class Card50 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card50';
    $this->faction = INDIAN;
  }
}
