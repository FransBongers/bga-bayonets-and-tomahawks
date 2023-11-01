<?php
namespace BayonetsAndTomahawks\Units;

class Anne extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ANNE;
    $this->counterText = clienttranslate('Anne');
    $this->faction = BRITISH;
  }
}
