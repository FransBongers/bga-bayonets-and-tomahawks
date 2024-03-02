<?php

namespace BayonetsAndTomahawks\Cards;

class Card42 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card42';
    $this->faction = FRENCH;
    $this->initiativeValue = 4;
  }
}
