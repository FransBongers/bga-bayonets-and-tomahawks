<?php
namespace BayonetsAndTomahawks\Spaces;

class Namaskonkik extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NAMASKONKIK;
    $this->battlePriority = 83;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Namaskonkik');
    $this->victorySpace = false;
    $this->top = 864.5;
    $this-> left = 586.5;
  }
}
