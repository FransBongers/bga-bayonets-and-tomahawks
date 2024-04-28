<?php
namespace BayonetsAndTomahawks\Spaces;

class Kithanink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KITHANINK;
    $this->battlePriority = 233;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kithanink');
    $this->victorySpace = false;
    $this->top = 1923;
    $this->left = 605;
    $this->adjacentSpaces = [
      ASSUNEPACHLA => ASSUNEPACHLA_KITHANINK,
      CAWICHNOWANE => CAWICHNOWANE_KITHANINK,
      FORKS_OF_THE_OHIO => FORKS_OF_THE_OHIO_KITHANINK,
      KAHNISTIOH => KAHNISTIOH_KITHANINK,
      LA_PRESQU_ISLE => KITHANINK_LA_PRESQU_ISLE,
    ];
  }
}
