<?php
namespace BayonetsAndTomahawks\Spaces;

class Zawakwtegok extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ZAWAKWTEGOK;
    $this->battlePriority = 102;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Zawakwtegok');
    $this->victorySpace = false;
    $this->top = 994.5;
    $this-> left = 812;
  }
}
