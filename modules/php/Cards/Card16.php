<?php

namespace BayonetsAndTomahawks\Cards;

class Card16 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card16';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 1;
    $this->years = [1755, 1756];
  }
}
