<?php
namespace BayonetsAndTomahawks\Spaces;

class NinetySix extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NINETY_SIX;
    $this->battlePriority = 292;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Ninety Six');
    $this->victorySpace = false;
    $this->top = 2216;
    $this->left = 1193.5;
    $this->adjacentSpaces = [
      CHARLES_TOWN => CHARLES_TOWN_NINETY_SIX,
      KEOWEE => KEOWEE_NINETY_SIX,
    ];
  }
}
