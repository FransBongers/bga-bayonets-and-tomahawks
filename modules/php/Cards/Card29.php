<?php

namespace BayonetsAndTomahawks\Cards;

class Card29 extends \BayonetsAndTomahawks\Models\Card
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
    $this->id = 'Card29';
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
  }
}
