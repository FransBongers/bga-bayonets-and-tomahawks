<?php
namespace BayonetsAndTomahawks\Units;

class RoyalScots17th extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ROYAL_SCOTS_17TH;
    $this->counterText = clienttranslate('Royal Scotts & 17th');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
