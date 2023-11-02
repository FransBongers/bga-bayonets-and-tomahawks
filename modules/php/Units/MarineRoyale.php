<?php
namespace BayonetsAndTomahawks\Units;

class MarineRoyale extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MARINE_ROYALE;
    $this->counterText = clienttranslate('Marine Royale');
    $this->faction = FRENCH;
  }
}
