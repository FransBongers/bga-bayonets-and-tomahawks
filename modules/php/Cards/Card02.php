<?php

namespace BayonetsAndTomahawks\Cards;

class Card02 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card02';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => ARMY_AP
      ]
    ];
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 5;
  }
}
