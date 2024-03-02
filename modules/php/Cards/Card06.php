<?php

namespace BayonetsAndTomahawks\Cards;

class Card06 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card06';
    $this->faction = BRITISH;
    $this->initiativeValue = 1;
    $this->years = [1757];
  }
}
