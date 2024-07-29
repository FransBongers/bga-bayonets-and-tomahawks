<?php
namespace BayonetsAndTomahawks\Units;

class Berry extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BERRY;
    $this->counterText = clienttranslate('Berry');
    $this->faction = FRENCH;
    $this->metropolitan = true;
    $this->officerGorget = true;
  }
}
