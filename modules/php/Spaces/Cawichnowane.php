<?php
namespace BayonetsAndTomahawks\Spaces;

class Cawichnowane extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAWICHNOWANE;
    $this->battlePriority = 213;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Cawichnowane');
    $this->victorySpace = false;
    $this->top = 1800;
    $this->left = 638;
    $this->adjacentSpaces = [
      ASSUNEPACHLA => ASSUNEPACHLA_CAWICHNOWANE,
      GNADENHUTTEN => CAWICHNOWANE_GNADENHUTTEN, 
      KAHNISTIOH => CAWICHNOWANE_KAHNISTIOH,
      KITHANINK => CAWICHNOWANE_KITHANINK,
      OQUAGA => CAWICHNOWANE_OQUAGA,
      SHAMOKIN => CAWICHNOWANE_SHAMOKIN,
    ];
  }
}
