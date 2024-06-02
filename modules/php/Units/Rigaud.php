<?php
namespace BayonetsAndTomahawks\Units;

class Rigaud extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = RIGAUD;
    $this->counterText = clienttranslate('Rigaud');
    $this->faction = FRENCH;
    $this->rating = 2;
    
  }
}
