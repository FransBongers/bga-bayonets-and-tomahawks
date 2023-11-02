<?php
namespace BayonetsAndTomahawks\Units;

class Cumberland extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CUMBERLAND;
    $this->counterText = clienttranslate('Cumberland');
    $this->faction = BRITISH;
  }
}
