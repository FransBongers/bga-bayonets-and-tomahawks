<?php

namespace BayonetsAndTomahawks\Cards;

class Card36 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card36';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP_2X
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => FRONTIERS_ABLAZE,
      'title' => clienttranslate('Frontiers Ablaze'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1755, 1756];
  }
}
