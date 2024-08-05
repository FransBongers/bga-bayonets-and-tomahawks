<?php

namespace BayonetsAndTomahawks\Cards;

class Card51 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card51';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->event = [
      'id' => BRITISH_ENCROACHMENT,
      'title' => clienttranslate('British Encroachment'),
      AR_START => true,
    ];
    $this->faction = INDIAN;
  }
}
