<?php

namespace BayonetsAndTomahawks\Cards;

class Card45 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card45';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->event = [
      'id' => SMALLPOX_EPIDEMIC,
      'title' => clienttranslate('Smallpox Epidemic'),
      AR_START => true,
    ];
    $this->faction = INDIAN;
  }
}
