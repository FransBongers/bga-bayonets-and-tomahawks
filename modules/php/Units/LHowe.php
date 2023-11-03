<?php
namespace BayonetsAndTomahawks\Units;

class LHowe extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = L_HOWE;
    $this->counterText = clienttranslate('Howe');
    $this->faction = BRITISH;
  }
}
