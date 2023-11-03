<?php
namespace BayonetsAndTomahawks\Spaces;

class Beverley extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BEVERLEY;
    $this->battlePriority = 271;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Beverley');
    $this->victorySpace = false;
    $this->top = 2106;
    $this-> left = 1049;
  }
}
