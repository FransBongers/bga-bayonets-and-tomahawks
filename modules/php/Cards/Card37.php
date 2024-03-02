<?php

namespace BayonetsAndTomahawks\Cards;

class Card37 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card37';
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1755, 1756];
  }
}
