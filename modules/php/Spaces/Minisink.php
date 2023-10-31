<?php
namespace BayonetsAndTomahawks\Spaces;

class Minisink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MINISINK;
    $this->battlePriority = 182;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Minisink');
    $this->victorySpace = false;
  }
}
