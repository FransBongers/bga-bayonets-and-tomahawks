<?php

namespace BayonetsAndTomahawks\Cards;

class Card28 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card28';
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1757];
  }
}
