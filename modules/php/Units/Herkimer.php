<?php
namespace BayonetsAndTomahawks\Units;

class Herkimer extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HERKIMER;
    $this->counterText = clienttranslate('Herkimer');
    $this->faction = BRITISH;
  }
}
