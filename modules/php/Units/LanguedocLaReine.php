<?php
namespace BayonetsAndTomahawks\Units;

class LanguedocLaReine extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LANGUEDOC_LA_REINE;
    $this->counterText = clienttranslate('Languedoc & La Reine');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
