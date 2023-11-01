<?php
namespace BayonetsAndTomahawks\Units;

class Mingo extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MINGO;
    $this->counterText = clienttranslate('Mingo');
    $this->faction = FRENCH;
    $this->indian = true;
  }
}
