<?php
namespace BayonetsAndTomahawks\Spaces;

class Oquaga extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OQUAGA;
    $this->battlePriority = 181;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Oquaga');
    $this->victorySpace = false;
    $this->top = 1626;
    $this-> left = 700;
  }
}
