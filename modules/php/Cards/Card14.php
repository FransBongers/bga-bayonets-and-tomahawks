<?php

namespace BayonetsAndTomahawks\Cards;

class Card14 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card14';
    $this->actionPoints = [
      [
        'id' => ARMY_AP
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
      'id' => PERFECT_VOLLEYS,
      'title' => clienttranslate('Perfect Volleys'),
      AR_START => false,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
