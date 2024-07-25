<?php
namespace BayonetsAndTomahawks\Units;

class VOWPickTwoArtilleryOrLightBritish extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH;
    $this->counterText = clienttranslate('Pick 2 Artillery OR 2 British Light units OR 1 of each');
    $this->faction = BRITISH;
  }
}
