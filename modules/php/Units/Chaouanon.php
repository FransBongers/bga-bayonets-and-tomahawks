<?php
namespace BayonetsAndTomahawks\Units;

class Chaouanon extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CHAOUANON;
    $this->counterText = clienttranslate('Chaouanon');
    $this->faction = INDIAN;
    $this->indian = true;
  }
}
