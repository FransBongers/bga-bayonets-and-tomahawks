<?php
namespace BayonetsAndTomahawks\Units;

class CHowe extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = C_HOWE;
    $this->counterText = clienttranslate('Howe');
    $this->faction = BRITISH;
    $this->rerollShapes = [TRIANGLE, SQUARE];
    $this->rating = 2;
  }
}
