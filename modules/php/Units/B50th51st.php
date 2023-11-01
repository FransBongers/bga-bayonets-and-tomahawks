<?php
namespace BayonetsAndTomahawks\Units;

class B50th51st extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_50TH_51ST;
    $this->counterText = clienttranslate('50th& 51st');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
