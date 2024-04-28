<?php
namespace BayonetsAndTomahawks\Spaces;

class Kahnistioh extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHNISTIOH;
    $this->battlePriority = 211;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Kahnistioh');
    $this->victorySpace = false;
    $this->top = 1785;
    $this->left = 530;
    $this->adjacentSpaces = [
      CAWICHNOWANE => CAWICHNOWANE_KAHNISTIOH,
      GENNISHEYO => GENNISHEYO_KAHNISTIOH,
      KITHANINK => KAHNISTIOH_KITHANINK,
    ];
  }
}
