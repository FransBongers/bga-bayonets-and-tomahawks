<?php
namespace BayonetsAndTomahawks\Units;

class Johnson extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = JOHNSON;
    $this->counterText = clienttranslate('Johnson');
    $this->faction = BRITISH;
  }
}
