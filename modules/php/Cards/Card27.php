<?php

namespace BayonetsAndTomahawks\Cards;

class Card27 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card27';
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1757];
  }
}
