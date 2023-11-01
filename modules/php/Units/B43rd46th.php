<?php
namespace BayonetsAndTomahawks\Units;

class B43rd46th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_43RD_46TH;
    $this->counterText = clienttranslate('43rd & 46th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
