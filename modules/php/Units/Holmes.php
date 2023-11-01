<?php
namespace BayonetsAndTomahawks\Units;

class Holmes extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HOLMES;
    $this->counterText = clienttranslate('Holmes');
    $this->faction = BRITISH;
  }
}
