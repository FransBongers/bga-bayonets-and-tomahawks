<?php
namespace BayonetsAndTomahawks\Units;

class VOWFewerTroopsFrench extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_FEWER_TROOPS_FRENCH;
    $this->counterText = clienttranslate('Fewer Troops');
    $this->faction = FRENCH;
  }
}
