<?php

namespace BayonetsAndTomahawks\Cards;

class Card27 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card27';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP_2X
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1757];
  }
}
