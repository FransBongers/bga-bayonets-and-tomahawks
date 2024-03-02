<?php

namespace BayonetsAndTomahawks\Cards;

class Card20 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card20';
    $this->faction = BRITISH;
    $this->initiativeValue = 5;
    $this->years = [1758, 1759];
  }
}
