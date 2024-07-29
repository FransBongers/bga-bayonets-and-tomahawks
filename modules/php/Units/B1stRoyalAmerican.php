<?php
namespace BayonetsAndTomahawks\Units;

class B1stRoyalAmerican extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_1ST_ROYAL_AMERICAN;
    $this->counterText = clienttranslate('1st Royal American');
    $this->faction = BRITISH;
    $this->metropolitan = true;
    $this->officerGorget = true;
  }
}
