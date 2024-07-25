<?php
namespace BayonetsAndTomahawks\Units;

class VOWPennsylvaniaMusters extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PENNSYLVANIA_MUSTERS;
    $this->counterText = clienttranslate('Pennsylvania Musters');
    $this->faction = BRITISH;
  }
}
