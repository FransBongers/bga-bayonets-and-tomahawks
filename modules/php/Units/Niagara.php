<?php
namespace BayonetsAndTomahawks\Units;

class Niagara extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = NIAGARA;
    $this->counterText = clienttranslate('Niagara');
    $this->faction = FRENCH;
  }
}
