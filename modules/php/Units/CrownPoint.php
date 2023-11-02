<?php
namespace BayonetsAndTomahawks\Units;

class CrownPoint extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CROWN_POINT;
    $this->counterText = clienttranslate('CrownPoint');
    $this->faction = BRITISH;
  }
}
