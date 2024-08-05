<?php

namespace BayonetsAndTomahawks\Cards;

class Card09 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card09';
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
        'id' => ARMY_AP
      ]
    ];
    $this->event = [
      'id' => SURPRISE_LANDING,
      'title' => clienttranslate('Surprise Landing'),
      AR_START => false,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }
}
