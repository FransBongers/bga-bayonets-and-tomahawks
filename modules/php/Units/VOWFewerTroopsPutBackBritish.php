<?php
namespace BayonetsAndTomahawks\Units;

class VOWFewerTroopsPutBackBritish extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_FEWER_TROOPS_PUT_BACK_BRITISH;
    $this->counterText = clienttranslate('Fewer Troops');
    $this->faction = BRITISH;
    $this->putTokenBackInPool = true;
  }
}
