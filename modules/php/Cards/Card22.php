<?php

namespace BayonetsAndTomahawks\Cards;

class Card22 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card22';
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 4;
  }
}
