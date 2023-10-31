<?php
namespace BayonetsAndTomahawks\Spaces;

class Saugink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SAUGINK;
    $this->battlePriority = 231;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Saugink');
    $this->victorySpace = false;
  }
}
