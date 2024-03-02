<?php

namespace BayonetsAndTomahawks\Cards;

class Card10 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card10';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
