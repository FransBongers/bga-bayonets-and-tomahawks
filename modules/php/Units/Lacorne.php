<?php
namespace BayonetsAndTomahawks\Units;

class Lacorne extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LACORNE;
    $this->counterText = clienttranslate('Lacorne');
    $this->faction = FRENCH;
  }
}
