<?php
namespace BayonetsAndTomahawks\Units;

class B22nd28th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_22ND_28TH;
    $this->counterText = clienttranslate('22nd & 28th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
