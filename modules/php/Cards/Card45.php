<?php

namespace BayonetsAndTomahawks\Cards;

class Card45 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card45';
    $this->faction = INDIAN;
  }
}
