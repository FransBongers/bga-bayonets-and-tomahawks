<?php

namespace BayonetsAndTomahawks\Cards;

class Card09 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card09';
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }
}
