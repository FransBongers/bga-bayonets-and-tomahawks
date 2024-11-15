<?php
namespace BayonetsAndTomahawks\Units;

class NYorkNJ extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->colony = NEW_YORK_AND_NEW_JERSEY;
    $this->counterId = NYORK_NJ;
    $this->counterText = clienttranslate('N.York & N.J.');
    $this->faction = BRITISH;
    $this->stackOrder = 4;
  }
}
