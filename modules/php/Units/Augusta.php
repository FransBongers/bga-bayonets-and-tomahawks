<?php
namespace BayonetsAndTomahawks\Units;

class Augusta extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = AUGUSTA;
    $this->counterText = clienttranslate('Augusta');
    $this->faction = BRITISH;
  }
}
