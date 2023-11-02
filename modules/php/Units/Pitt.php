<?php
namespace BayonetsAndTomahawks\Units;

class Pitt extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = PITT;
    $this->counterText = clienttranslate('Pitt');
    $this->faction = BRITISH;
  }
}
