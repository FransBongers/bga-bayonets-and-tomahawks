<?php

namespace BayonetsAndTomahawks\Cards;

class Card49 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card49';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->event = [
      'id' => PURSUIT_OF_ELEVATED_STATUS,
      'title' => clienttranslate('Pursuit of Elevated Status'),
      AR_START => false,
    ];
    $this->faction = INDIAN;
  }
}
