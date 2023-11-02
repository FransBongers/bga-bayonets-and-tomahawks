<?php
namespace BayonetsAndTomahawks\Units;

class Campbell extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CAMPBELL;
    $this->counterText = clienttranslate('Campbell');
    $this->faction = BRITISH;
    $this->highland = true;
    $this->metropolitan = true;
  }
}
