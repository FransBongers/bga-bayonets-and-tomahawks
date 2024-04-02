<?php

namespace BayonetsAndTomahawks\Cards;

class Card25 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card25';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP_2X
      ],
    ];
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 6;
  }
}
