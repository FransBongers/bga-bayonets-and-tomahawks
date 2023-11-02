<?php
namespace BayonetsAndTomahawks\Units;

class Stanwix extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = STANWIX;
    $this->counterText = clienttranslate('Stanwix');
    $this->faction = BRITISH;
  }
}
