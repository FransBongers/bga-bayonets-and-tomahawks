<?php

namespace BayonetsAndTomahawks\Cards;

class Card39 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card39';
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
    $this->event = [
      'id' => FRENCH_LAKE_WARSHIPS,
      'title' => clienttranslate('French Lake Warships'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1758,1759];
  }
}
