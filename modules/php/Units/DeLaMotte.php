<?php
namespace BayonetsAndTomahawks\Units;

class DeLaMotte extends \BayonetsAndTomahawks\Models\Fleet
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DE_LA_MOTTE;
    $this->counterText = clienttranslate('De La Motte');
    $this->faction = FRENCH;
  }
}
