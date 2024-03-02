<?php

namespace BayonetsAndTomahawks\Cards;

class Card11 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card11';
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
