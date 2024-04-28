<?php
namespace BayonetsAndTomahawks\Units;

class Outaouais extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = OUTAOUAIS;
    $this->counterText = clienttranslate('Outaouais');
    $this->faction = FRENCH;
    $this->indian = true;
  }
}
