<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;

class Card30 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card30';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => RELUCTANT_WAGONEERS,
      'title' => clienttranslate('Reluctant Wagoneers'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 3;
  }

  public function resolveARStart($ctx)
  {
    Globals::setLostAPBritish([ARMY_AP, ARMY_AP]);
  }
}
