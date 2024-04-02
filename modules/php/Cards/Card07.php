<?php

namespace BayonetsAndTomahawks\Cards;

class Card07 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card07';
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
        'id' => ARMY_AP_2X
      ]
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 2;
  }
}
