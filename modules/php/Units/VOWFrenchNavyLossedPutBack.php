<?php
namespace BayonetsAndTomahawks\Units;

class VOWFrenchNavyLossedPutBack extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_FRENCH_NAVY_LOSSES_PUT_BACK;
    $this->counterText = clienttranslate('French Navy Losses');
    $this->faction = FRENCH;
    $this->putTokenBackInPool = true;
  }
}
