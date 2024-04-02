<?php

namespace BayonetsAndTomahawks\Cards;

class Card05 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->id = 'Card05';
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 6;
  }
}
