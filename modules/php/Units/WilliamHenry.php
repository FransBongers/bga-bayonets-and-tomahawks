<?php
namespace BayonetsAndTomahawks\Units;

class WilliamHenry extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = WILLIAM_HENRY;
    $this->counterText = clienttranslate('William Henry');
    $this->faction = BRITISH;
  }
}
