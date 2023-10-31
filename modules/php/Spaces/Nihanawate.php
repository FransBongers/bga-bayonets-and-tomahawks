<?php
namespace BayonetsAndTomahawks\Spaces;

class Nihanawate extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIHANAWATE;
    $this->battlePriority = 142;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Nihanawate');
    $this->victorySpace = false;
  }
}
