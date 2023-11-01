<?php
namespace BayonetsAndTomahawks\Units;

class NYorkNJ extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = NYORK_NJ;
    $this->counterText = clienttranslate('N.York & N.J.');
    $this->faction = BRITISH;
  }
}
