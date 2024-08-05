<?php

namespace BayonetsAndTomahawks\Cards;

class Card33 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card33';
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
    $this->event = [
      'id' => HESITANT_BRITISH_GENERAL,
      'title' => clienttranslate('Hesitant British General'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1757];
  }
}
