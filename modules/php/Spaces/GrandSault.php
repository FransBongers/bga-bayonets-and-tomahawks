<?php
namespace BayonetsAndTomahawks\Spaces;

class GrandSault extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GRAND_SAULT;
    $this->battlePriority = 51;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Grand Sault');
    $this->victorySpace = false;
  }
}
