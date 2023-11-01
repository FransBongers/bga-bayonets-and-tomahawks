<?php
namespace BayonetsAndTomahawks\Units;

class Delaware extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DELAWARE;
    $this->counterText = clienttranslate('Delaware');
    $this->faction = FRENCH;
    $this->indian = true;
  }
}
