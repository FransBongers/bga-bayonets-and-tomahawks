<?php
namespace BayonetsAndTomahawks\Units;

class B27th55th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_27TH_55TH;
    $this->counterText = clienttranslate('27th & 55th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
    $this->officerGorget = true;
  }
}
