<?php
namespace BayonetsAndTomahawks\Units;

class FoixQuercy extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FOIX_QUERCY;
    $this->counterText = clienttranslate('Foix & Quercy');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
