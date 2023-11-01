<?php
namespace BayonetsAndTomahawks\Units;

class B40th45th47th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_40TH_45TH_47TH;
    $this->counterText = clienttranslate('40th & 45th & 47th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
