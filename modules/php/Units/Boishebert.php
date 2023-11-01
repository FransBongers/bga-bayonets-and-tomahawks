<?php
namespace BayonetsAndTomahawks\Units;

class Boishebert extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BOISHEBERT;
    $this->counterText = clienttranslate('Boishébert');
    $this->faction = FRENCH;
  }
}
