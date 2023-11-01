<?php
namespace BayonetsAndTomahawks\Units;

class Durell extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DURELL;
    $this->counterText = clienttranslate('Durell');
    $this->faction = BRITISH;
  }
}
