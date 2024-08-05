<?php

namespace BayonetsAndTomahawks\Cards;

class Card08 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card08';
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
    $this->event = [
      'id' => LUCKY_CANNONBALL,
      'title' => clienttranslate('Lucky Cannonball'),
      AR_START => false,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 2;
  }
}
