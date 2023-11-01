<?php
namespace BayonetsAndTomahawks\Units;

class Beausejour extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEAUSEJOUR;
    $this->counterText = clienttranslate('Beauséjour');
    $this->faction = FRENCH;
  }
}
