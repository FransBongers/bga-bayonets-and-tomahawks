<?php

namespace BayonetsAndTomahawks\Cards;

class Card41 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card41';
    $this->actionPoints = [
      [
        'id' => FRENCH_LIGHT_ARMY_AP
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
    $this->years = [1758, 1759];
  }
}
