<?php
namespace BayonetsAndTomahawks\Units;

class Montgomery extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MONTGOMERY;
    $this->counterText = clienttranslate('Montgomery');
    $this->faction = BRITISH;
    $this->highland = true;
    $this->metropolitan = true;
  }
}
