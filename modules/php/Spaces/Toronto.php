<?php
namespace BayonetsAndTomahawks\Spaces;

class Toronto extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TORONTO;
    $this->battlePriority = 191;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Toronto');
    $this->victorySpace = false;
  }
}
