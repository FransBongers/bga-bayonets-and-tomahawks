<?php

namespace BayonetsAndTomahawks\Cards;

class Card06 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card06';
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
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => DELAYED_SUPPLIES_FROM_FRANCE,
      'title' => clienttranslate('Delayed Supplies from France'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 1;
    $this->years = [1757];
  }
}
