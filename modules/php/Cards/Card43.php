<?php

namespace BayonetsAndTomahawks\Cards;

class Card43 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card43';
    $this->faction = INDIAN;
  }
}
