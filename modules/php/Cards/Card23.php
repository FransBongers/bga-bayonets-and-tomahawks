<?php

namespace BayonetsAndTomahawks\Cards;

class Card23 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card23';
    $this->actionPoints = [
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->buildUpDeck = true;
    $this->faction = FRENCH;
    $this->initiativeValue = 5;
  }
}
