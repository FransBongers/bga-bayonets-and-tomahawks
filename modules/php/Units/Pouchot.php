<?php
namespace BayonetsAndTomahawks\Units;

class Pouchot extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = POUCHOT;
    $this->counterText = clienttranslate('Pouchot');
    $this->faction = FRENCH;
    $this->rating = 1;
    $this->rerollShapes = [SQUARE];
  }
}
