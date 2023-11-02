<?php
namespace BayonetsAndTomahawks\Units;

class Ticonderoga extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = TICONDEROGA;
    $this->counterText = clienttranslate('Ticonderoga');
    $this->faction = BRITISH;
  }
}
