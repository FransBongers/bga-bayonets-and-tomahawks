<?php

namespace BayonetsAndTomahawks\Cards;

class Card23 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card23';
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 5;
  }
}
