<?php

namespace BayonetsAndTomahawks\Cards;

class Card10 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card10';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->buildUpDeck = true;
    $this->event = [
      'id' => FORCED_MARCH,
      'title' => clienttranslate('Forced March'),
      AR_START => false,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
