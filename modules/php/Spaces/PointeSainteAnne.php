<?php
namespace BayonetsAndTomahawks\Spaces;

class PointeSainteAnne extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = POINTE_SAINTE_ANNE;
    $this->battlePriority = 52;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Pointe Sainte Anne');
    $this->victorySpace = false;
  }
}
