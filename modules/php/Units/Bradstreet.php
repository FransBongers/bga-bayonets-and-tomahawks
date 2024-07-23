<?php

namespace BayonetsAndTomahawks\Units;

class Bradstreet extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BRADSTREET;
    $this->counterText = clienttranslate('Bradstreet');
    $this->faction = BRITISH;
    $this->rerollShapes = [SQUARE];
    $this->rating = 2;
  }
}
