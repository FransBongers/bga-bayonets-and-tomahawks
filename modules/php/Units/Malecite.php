<?php
namespace BayonetsAndTomahawks\Units;

class Malecite extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MALECITE;
    $this->counterText = clienttranslate('Malécite');
    $this->faction = FRENCH;
    $this->indian = true;
  }
}
