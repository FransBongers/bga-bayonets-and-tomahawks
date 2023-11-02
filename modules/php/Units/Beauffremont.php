<?php
namespace BayonetsAndTomahawks\Units;

class Beauffremont extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEAUFFREMONT;
    $this->counterText = clienttranslate('Beauffremont');
    $this->faction = FRENCH;
  }
}
