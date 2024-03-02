<?php

namespace BayonetsAndTomahawks\Cards;

class Card13 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card13';
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }
}
