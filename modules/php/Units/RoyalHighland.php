<?php
namespace BayonetsAndTomahawks\Units;

class RoyalHighland extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ROYAL_HIGHLAND;
    $this->counterText = clienttranslate('Royal Highland');
    $this->faction = BRITISH;
    $this->highland = true;
    $this->metropolitan = true;
  }
}
