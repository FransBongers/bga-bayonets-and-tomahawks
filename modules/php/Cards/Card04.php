<?php

namespace BayonetsAndTomahawks\Cards;

class Card04 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card04';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 6;
  }
}
