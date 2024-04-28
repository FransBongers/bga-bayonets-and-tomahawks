<?php
namespace BayonetsAndTomahawks\Units;

class Kahnawake extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = KAHNAWAKE;
    $this->counterText = clienttranslate('Kahnawake');
    $this->faction = INDIAN;
    $this->indian = true;
  }
}
