<?php
namespace BayonetsAndTomahawks\Units;

class Boscawen extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BOSCAWEN;
    $this->counterText = clienttranslate('Boscawen');
    $this->faction = BRITISH;
  }
}
