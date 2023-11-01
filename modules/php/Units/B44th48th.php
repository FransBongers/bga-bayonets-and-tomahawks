<?php
namespace BayonetsAndTomahawks\Units;

class B44th48th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_44TH_48TH;
    $this->counterText = clienttranslate('44th & 48th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
