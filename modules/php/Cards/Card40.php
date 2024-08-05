<?php

namespace BayonetsAndTomahawks\Cards;

class Card40 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card40';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP_2X
      ],
    ];
    $this->event = [
      'id' => ROUGH_SEAS,
      'title' => clienttranslate('Rough Seas'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1758, 1759];
  }
}
