<?php
namespace BayonetsAndTomahawks\Units;

class Ontario extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ONTARIO;
    $this->counterText = clienttranslate('Ontario');
    $this->faction = BRITISH;
  }
}
