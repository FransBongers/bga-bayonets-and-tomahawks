<?php

namespace BayonetsAndTomahawks\Cards;

class Card32 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WILDERNESS_AMBUSH_CARD_ID;
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => WILDERNESS_AMBUSH,
      'title' => clienttranslate('Wilderness Ambush'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 4;
  }
}
