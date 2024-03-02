<?php

namespace BayonetsAndTomahawks\Cards;

class Card08 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card08';
    $this->faction = BRITISH;
    $this->initiativeValue = 2;
  }
}
