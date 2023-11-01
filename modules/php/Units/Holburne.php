<?php
namespace BayonetsAndTomahawks\Units;

class Holburne extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HOLBURNE;
    $this->counterText = clienttranslate('Holburne');
    $this->faction = BRITISH;
  }
}
