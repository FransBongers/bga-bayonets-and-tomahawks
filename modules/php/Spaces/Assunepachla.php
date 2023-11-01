<?php
namespace BayonetsAndTomahawks\Spaces;

class Assunepachla extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA;
    $this->battlePriority = 232;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Assunepachla');
    $this->victorySpace = false;
  }
}
