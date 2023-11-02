<?php
namespace BayonetsAndTomahawks\Units;

class Scott extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = SCOTT;
    $this->counterText = clienttranslate('Scott');
    $this->faction = BRITISH;
  }
}
