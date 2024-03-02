<?php

namespace BayonetsAndTomahawks\Cards;

class Card39 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card39';
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1758,1759];
  }
}
