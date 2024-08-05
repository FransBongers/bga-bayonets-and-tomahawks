<?php

namespace BayonetsAndTomahawks\Cards;

class Card37 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card37';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP_2X
      ],
    ];
    $this->event = [
      'id' => COUP_DE_MAIN,
      'title' => clienttranslate('Coup de Main'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1755, 1756];
  }
}
