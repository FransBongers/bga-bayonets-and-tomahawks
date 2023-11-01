<?php
namespace BayonetsAndTomahawks\Units;

class B94th95th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_94TH_95TH;
    $this->counterText = clienttranslate('94th & 95th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
