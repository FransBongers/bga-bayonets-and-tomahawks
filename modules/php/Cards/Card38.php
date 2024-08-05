<?php

namespace BayonetsAndTomahawks\Cards;

class Card38 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card38';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => IROQUOIS_DIPLOMACY,
      'title' => clienttranslate('Iroquois Diplomacy'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1755,1756];
  }
}
