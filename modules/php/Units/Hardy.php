<?php
namespace BayonetsAndTomahawks\Units;

class Hardy extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HARDY;
    $this->counterText = clienttranslate('Hardy');
    $this->faction = BRITISH;
  }
}
