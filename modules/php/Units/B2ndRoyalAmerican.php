<?php
namespace BayonetsAndTomahawks\Units;

class B2ndRoyalAmerican extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_2ND_ROYAL_AMERICAN;
    $this->counterText = clienttranslate('2nd Royal American');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
