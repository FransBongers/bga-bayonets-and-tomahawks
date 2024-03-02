<?php

namespace BayonetsAndTomahawks\Cards;

class Card03 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card03';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 5;
  }
}
