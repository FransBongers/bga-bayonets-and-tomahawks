<?php
namespace BayonetsAndTomahawks\Units;

class VolontEtrangersCambis extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOLONT_ETRANGERS_CAMBIS;
    $this->counterText = clienttranslate('Volont. Étrangers & Cambis');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
