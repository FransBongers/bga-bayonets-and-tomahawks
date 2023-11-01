<?php
namespace BayonetsAndTomahawks\Units;

class Lignery extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LIGNERY;
    $this->counterText = clienttranslate('Lignery');
    $this->faction = FRENCH;
  }
}
