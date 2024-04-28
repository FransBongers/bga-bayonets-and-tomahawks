<?php
namespace BayonetsAndTomahawks\Units;

class Mississague extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = MISSISSAGUE;
    $this->counterText = clienttranslate('Mississagué');
    $this->faction = INDIAN;
    $this->indian = true;
  }
}
