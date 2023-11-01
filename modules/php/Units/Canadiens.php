<?php
namespace BayonetsAndTomahawks\Units;

class Canadiens extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CANADIENS;
    $this->counterText = clienttranslate('Canadiens');
    $this->faction = FRENCH;
  }
}
