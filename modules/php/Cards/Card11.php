<?php

namespace BayonetsAndTomahawks\Cards;

class Card11 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card11';
    $this->actionPoints = [
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP_2X
      ]
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
