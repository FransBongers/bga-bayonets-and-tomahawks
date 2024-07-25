<?php
namespace BayonetsAndTomahawks\Units;

class VOWPickOneArtilleryFrench extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PICK_ONE_ARTILLERY_FRENCH;
    $this->counterText = clienttranslate('Pick 1 Artillery unit');
    $this->faction = FRENCH;
  }
}
