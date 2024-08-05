<?php

namespace BayonetsAndTomahawks\Cards;

class Card54 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card54';
    $this->event = [
      'id' => LETS_SEE_HOW_THE_FRENCH_FIGHT,
      'title' => clienttranslate("Let's See How the French Fight"),
      AR_START => false,
    ];
    $this->faction = INDIAN;
  }
}
