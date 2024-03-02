<?php

namespace BayonetsAndTomahawks\Cards;

class Card26 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card26';
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 6;
  }
}
