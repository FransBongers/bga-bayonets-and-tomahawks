<?php
namespace BayonetsAndTomahawks\Units;

class BeaujeuLacorne extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEAUJEU_LACORNE;
    $this->counterText = clienttranslate('Beaujeu Lacorne');
    $this->faction = FRENCH;
  }
}
