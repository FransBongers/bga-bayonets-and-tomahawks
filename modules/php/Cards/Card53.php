<?php

namespace BayonetsAndTomahawks\Cards;

class Card53 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card53';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->faction = INDIAN;
  }
}
