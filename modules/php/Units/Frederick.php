<?php
namespace BayonetsAndTomahawks\Units;

class Frederick extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FREDERICK;
    $this->counterText = clienttranslate('Frederick');
    $this->faction = BRITISH;
  }
}
