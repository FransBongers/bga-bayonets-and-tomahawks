<?php

namespace BayonetsAndTomahawks\Cards;

class Card34 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card34';
    $this->faction = FRENCH;
    $this->initiativeValue = 3;
  }
}
