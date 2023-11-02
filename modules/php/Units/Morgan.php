<?php
namespace BayonetsAndTomahawks\Units;

class Morgan extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MORGAN;
    $this->counterText = clienttranslate('Morgan');
    $this->faction = BRITISH;
  }
}
