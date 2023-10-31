<?php
namespace BayonetsAndTomahawks\Spaces;

class Kadesquit extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KADESQUIT;
    $this->battlePriority = 72;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kadesquit');
    $this->victorySpace = false;
  }
}
