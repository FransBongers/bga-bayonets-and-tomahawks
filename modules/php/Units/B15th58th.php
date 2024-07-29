<?php
namespace BayonetsAndTomahawks\Units;

class B15th58th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_15TH_58TH;
    $this->counterText = clienttranslate('15th & 58th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
    $this->officerGorget = true;
  }
}
