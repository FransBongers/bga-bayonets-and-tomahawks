<?php
namespace BayonetsAndTomahawks\Spaces;

class Kithanink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KITHANINK;
    $this->battlePriority = 233;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Kithanink');
    $this->outpost = true;
    $this->value = 1;
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
