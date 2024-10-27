<?php
namespace BayonetsAndTomahawks\Units;

class VOWPickOneColonialLightPutBack extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK;
    $this->counterText = clienttranslate('Pick 1 Colonial Light unit');
    $this->faction = BRITISH;
    $this->putTokenBackInPool = true;
  }
}
