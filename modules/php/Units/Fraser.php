<?php
namespace BayonetsAndTomahawks\Units;

class Fraser extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = FRASER;
    $this->counterText = clienttranslate('Fraser');
    $this->faction = BRITISH;
    $this->highland = true;
    $this->metropolitan = true;
    $this->officerGorget = true;
  }
}
