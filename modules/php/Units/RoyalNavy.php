<?php
namespace BayonetsAndTomahawks\Units;

class RoyalNavy extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ROYAL_NAVY;
    $this->counterText = clienttranslate('Royal Navy');
    $this->faction = BRITISH;
  }
}
