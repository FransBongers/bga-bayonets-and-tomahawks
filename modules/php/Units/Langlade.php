<?php
namespace BayonetsAndTomahawks\Units;

class Langlade extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LANGLADE;
    $this->counterText = clienttranslate('Langlade');
    $this->faction = FRENCH;
  }
}
