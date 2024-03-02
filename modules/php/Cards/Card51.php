<?php

namespace BayonetsAndTomahawks\Cards;

class Card51 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card51';
    $this->faction = INDIAN;
  }
}
