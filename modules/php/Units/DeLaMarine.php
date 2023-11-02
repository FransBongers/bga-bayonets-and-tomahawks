<?php
namespace BayonetsAndTomahawks\Units;

class DeLaMarine extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = DE_LA_MARINE;
    $this->counterText = clienttranslate('De La Marine');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
