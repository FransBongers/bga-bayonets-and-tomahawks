<?php

namespace BayonetsAndTomahawks\Cards;

class Card48 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card48';
    $this->faction = INDIAN;
  }
}
