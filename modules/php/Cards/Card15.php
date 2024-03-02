<?php

namespace BayonetsAndTomahawks\Cards;

class Card15 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card15';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
    $this->years = [1755, 1756];
  }
}
