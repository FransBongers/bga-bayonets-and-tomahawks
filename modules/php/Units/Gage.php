<?php
namespace BayonetsAndTomahawks\Units;

class Gage extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = GAGE;
    $this->counterText = clienttranslate('Gage');
    $this->faction = BRITISH;
  }
}
