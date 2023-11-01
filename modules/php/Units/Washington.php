<?php
namespace BayonetsAndTomahawks\Units;

class Washington extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = WASHINGTON;
    $this->counterText = clienttranslate('Washington');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
