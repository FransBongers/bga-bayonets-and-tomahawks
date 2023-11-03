<?php
namespace BayonetsAndTomahawks\Spaces;

class LeBaril extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_BARIL;
    $this->battlePriority = 273;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Le Baril');
    $this->victorySpace = false;
    $this->top = 2134;
    $this-> left = 458;
  }
}
