<?php
namespace BayonetsAndTomahawks\Units;

class Bastion extends \BayonetsAndTomahawks\Models\BastionModel
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = BASTION;
    $this->counterId = BASTION;
    $this->counterText = clienttranslate('Bastion');
    $this->faction = FRENCH;
  }
}
