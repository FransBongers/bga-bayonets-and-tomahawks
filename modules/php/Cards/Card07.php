<?php

namespace BayonetsAndTomahawks\Cards;

class Card07 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card07';
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
        'id' => ARMY_AP_2X
      ]
    ];
    $this->event = [
      'id' => CONSTRUCTION_FRENZY,
      'title' => clienttranslate('Construction Frenzy'),
      AR_START => false,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 2;
  }
}
