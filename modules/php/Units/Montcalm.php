<?php
namespace BayonetsAndTomahawks\Units;

class Montcalm extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MONTCALM;
    $this->counterText = clienttranslate('Montcalm');
    $this->faction = FRENCH;
    $this->rating = 3;
    $this->rerollShapes = [SQUARE];
  }
}
