<?php

namespace BayonetsAndTomahawks\Cards;

class Card14 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card14';
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
