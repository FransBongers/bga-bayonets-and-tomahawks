<?php
namespace BayonetsAndTomahawks\Units;

class VOWPickTwoArtilleryBritish extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PICK_TWO_ARTILLERY_BRITISH;
    $this->counterText = clienttranslate('Pick 2 Artillery units');
    $this->faction = BRITISH;
  }
}
