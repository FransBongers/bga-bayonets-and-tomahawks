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
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }
}
