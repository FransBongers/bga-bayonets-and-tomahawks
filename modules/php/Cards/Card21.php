<?php

namespace BayonetsAndTomahawks\Cards;

class Card21 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card21';
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
  }
}
