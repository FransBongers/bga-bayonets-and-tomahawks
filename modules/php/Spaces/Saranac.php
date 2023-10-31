<?php
namespace BayonetsAndTomahawks\Spaces;

class Saranac extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SARANAC;
    $this->battlePriority = 132;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Saranac');
    $this->victorySpace = false;
  }
}
