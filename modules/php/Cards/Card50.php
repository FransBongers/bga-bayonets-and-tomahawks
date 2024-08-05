<?php

namespace BayonetsAndTomahawks\Cards;

class Card50 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card50';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->event = [
      'id' => A_RIGHT_TO_PLUNDER_AND_CAPTIVES,
      'title' => clienttranslate('A Right to Plunder & Captives'),
      AR_START => false,
    ];
    $this->faction = INDIAN;
  }
}
