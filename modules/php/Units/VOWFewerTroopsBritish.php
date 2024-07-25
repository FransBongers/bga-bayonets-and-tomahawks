<?php
namespace BayonetsAndTomahawks\Units;

class VOWFewerTroopsBritish extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_FEWER_TROOPS_BRITISH;
    $this->counterText = clienttranslate('Fewer Troops');
    $this->faction = BRITISH;
  }
}
