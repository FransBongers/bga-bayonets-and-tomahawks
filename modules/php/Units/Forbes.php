<?php
namespace BayonetsAndTomahawks\Units;

class Forbes extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FORBES;
    $this->counterText = clienttranslate('Forbes');
    $this->faction = BRITISH;
  }
}
