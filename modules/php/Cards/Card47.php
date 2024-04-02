<?php

namespace BayonetsAndTomahawks\Cards;

class Card47 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card47';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->faction = INDIAN;
  }
}
