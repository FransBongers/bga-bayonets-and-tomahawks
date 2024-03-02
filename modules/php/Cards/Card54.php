<?php

namespace BayonetsAndTomahawks\Cards;

class Card54 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card54';
    $this->faction = INDIAN;
  }
}
