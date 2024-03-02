<?php

namespace BayonetsAndTomahawks\Cards;

class Card17 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card17';
    $this->faction = BRITISH;
    $this->initiativeValue = 1;
    $this->years = [1755, 1756];
  }
}
