<?php
namespace BayonetsAndTomahawks\Spaces;

class Shamokin extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SHAMOKIN;
    $this->battlePriority = 221;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Shamokin');
    $this->victorySpace = false;
    $this->top = 1828;
    $this-> left = 813.5;
  }
}
