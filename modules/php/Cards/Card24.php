<?php

namespace BayonetsAndTomahawks\Cards;

class Card24 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card24';
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 5;
  }
}
