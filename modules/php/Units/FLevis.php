<?php
namespace BayonetsAndTomahawks\Units;

class FLevis extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = F_LEVIS;
    $this->counterText = clienttranslate('Lévis');
    $this->faction = FRENCH;
  }
}
