<?php

namespace BayonetsAndTomahawks\Cards;

class Card13 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card13';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP_2X
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => CHEROKEE_DIPLOMACY,
      'title' => clienttranslate('Cherokee Diplomacy'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }
}
