<?php

namespace BayonetsAndTomahawks\Cards;

class Card32 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card32';
    $this->faction = FRENCH;
    $this->initiativeValue = 4;
  }
}
