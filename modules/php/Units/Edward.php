<?php
namespace BayonetsAndTomahawks\Units;

class Edward extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = EDWARD;
    $this->counterText = clienttranslate('Edward');
    $this->faction = BRITISH;
  }
}
