<?php

namespace BayonetsAndTomahawks\Cards;

class Card28 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card28';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP_2X
      ],
    ];
    $this->event = [
      'id' => CONSTRUCTION_FRENZY,
      'title' => clienttranslate('Construction Frenzy'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1757];
  }
}
