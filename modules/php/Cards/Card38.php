<?php

namespace BayonetsAndTomahawks\Cards;

class Card38 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card38';
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1755,1756];
  }
}
