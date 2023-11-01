<?php
namespace BayonetsAndTomahawks\Units;

class Howe extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HOWE;
    $this->counterText = clienttranslate('Howe');
    $this->faction = BRITISH;
  }
}
