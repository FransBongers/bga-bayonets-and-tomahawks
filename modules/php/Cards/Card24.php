<?php

namespace BayonetsAndTomahawks\Cards;

class Card24 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card24';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
    ];
    $this->buildUpDeck = true;
    $this->event = [
      'id' => FORCED_MARCH,
      'title' => clienttranslate('Forced March'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 5;
  }
}
