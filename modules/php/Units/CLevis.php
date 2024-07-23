<?php
namespace BayonetsAndTomahawks\Units;

class CLevis extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = C_LEVIS;
    $this->counterText = clienttranslate('Lévis');
    $this->faction = FRENCH;
    $this->rating = 3;
    $this->rerollShapes = [TRIANGLE, SQUARE];
  }
}
