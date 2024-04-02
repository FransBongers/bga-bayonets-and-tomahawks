<?php

namespace BayonetsAndTomahawks\Cards;

class Card18 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card18';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->buildUpDeck = true;
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
    $this->years = [1758, 1759];
  }
}
