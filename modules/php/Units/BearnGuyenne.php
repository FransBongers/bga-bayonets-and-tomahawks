<?php
namespace BayonetsAndTomahawks\Units;

class BearnGuyenne extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEARN_GUYENNE;
    $this->counterText = clienttranslate('Bearn & Guyenne');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
