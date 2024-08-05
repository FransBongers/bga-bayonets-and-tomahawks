<?php

namespace BayonetsAndTomahawks\Cards;

class Card20 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card20';
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
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => FRENCH_TRADE_GOODS_DESTROYED,
      'title' => clienttranslate('French Trade Goods Destroyed'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 5;
    $this->years = [1758, 1759];
  }
}
