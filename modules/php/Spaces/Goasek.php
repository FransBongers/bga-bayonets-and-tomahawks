<?php
namespace BayonetsAndTomahawks\Spaces;

class Goasek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK;
    $this->battlePriority = 112;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Goasek');
    $this->victorySpace = false;
  }
}
