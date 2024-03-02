<?php

namespace BayonetsAndTomahawks\Cards;

class Card07 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card07';
    $this->faction = BRITISH;
    $this->initiativeValue = 2;
  }
}
