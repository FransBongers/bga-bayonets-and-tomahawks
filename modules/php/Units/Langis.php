<?php
namespace BayonetsAndTomahawks\Units;

class Langis extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LANGIS;
    $this->counterText = clienttranslate('Langis');
    $this->faction = FRENCH;
  }
}
