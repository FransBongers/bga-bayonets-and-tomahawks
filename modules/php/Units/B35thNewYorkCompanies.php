<?php
namespace BayonetsAndTomahawks\Units;

class B35thNewYorkCompanies extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_35TH_NEW_YORK_COMPANIES;
    $this->counterText = clienttranslate('35th & New York Companies');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
