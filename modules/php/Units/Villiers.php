<?php
namespace BayonetsAndTomahawks\Units;

class Villiers extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VILLIERS;
    $this->counterText = clienttranslate('Villiers');
    $this->faction = FRENCH;
  }
}
