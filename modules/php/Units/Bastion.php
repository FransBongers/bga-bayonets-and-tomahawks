<?php
namespace BayonetsAndTomahawks\Units;

class Bastion extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BASTION;
    $this->counterText = clienttranslate('No Retreat');
    $this->faction = FRENCH;
  }
}
