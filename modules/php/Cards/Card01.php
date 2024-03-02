<?php

namespace BayonetsAndTomahawks\Cards;

class Card01 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card01';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
    $this->years = [1757];
  }
}
