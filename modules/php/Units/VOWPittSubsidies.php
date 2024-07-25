<?php
namespace BayonetsAndTomahawks\Units;

class VOWPittSubsidies extends \BayonetsAndTomahawks\Models\VagariesOfWar
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = VOW_PITT_SUBSIDIES;
    $this->counterText = clienttranslate('Pitt Subsidies');
    $this->faction = BRITISH;
  }
}
