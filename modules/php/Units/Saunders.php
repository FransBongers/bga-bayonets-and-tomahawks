<?php
namespace BayonetsAndTomahawks\Units;

class Saunders extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = SAUNDERS;
    $this->counterText = clienttranslate('Saunders');
    $this->faction = BRITISH;
  }
}
