<?php
namespace BayonetsAndTomahawks\Units;

class Mohawk extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MOHAWK;
    $this->counterText = clienttranslate('Mohawk');
    $this->faction = BRITISH;
    $this->indian = true;
  }
}
